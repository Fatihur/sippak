import express from 'express';
import cors from 'cors';
import qrcode from 'qrcode';
import pino from 'pino';
import { Boom } from '@hapi/boom';
import makeWASocket, {
    DisconnectReason,
    fetchLatestBaileysVersion,
    useMultiFileAuthState,
} from '@whiskeysockets/baileys';

const app = express();
const port = Number(process.env.WA_GATEWAY_PORT || 3010);
const apiToken = process.env.WA_GATEWAY_TOKEN || 'sippak-local-token';
const authDir = process.env.WA_AUTH_DIR || './wa-gateway/storage/auth';
const logger = pino({ level: process.env.WA_LOG_LEVEL || 'silent' });

let sock = null;
let qrText = null;
let qrDataUrl = null;
let connectionStatus = 'starting';
let connectedNumber = null;
let lastDisconnectReason = null;
let reconnecting = false;

app.use(cors());
app.use(express.json({ limit: '10mb' }));

function requireToken(req, res, next) {
    const bearer = req.headers.authorization?.replace('Bearer ', '');
    const token = bearer || req.headers['x-api-token'] || req.query.token;
    if (token !== apiToken) {
        return res.status(401).json({ success: false, message: 'Token API tidak valid.' });
    }
    return next();
}

function normalizeNumber(number) {
    let clean = String(number || '').replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) clean = '62' + clean.slice(1);
    if (!clean.startsWith('62')) clean = '62' + clean;
    return `${clean}@s.whatsapp.net`;
}

async function startSocket() {
    if (reconnecting) return;
    reconnecting = true;
    try {
        const { state, saveCreds } = await useMultiFileAuthState(authDir);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            auth: state,
            logger,
            printQRInTerminal: false,
            browser: ['SILAPAK', 'Chrome', '1.0.0'],
        });

        sock.ev.on('creds.update', saveCreds);
        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrText = qr;
                qrDataUrl = await qrcode.toDataURL(qr, { margin: 1, width: 320 });
                connectionStatus = 'qr';
                connectedNumber = null;
            }

            if (connection === 'open') {
                connectionStatus = 'connected';
                qrText = null;
                qrDataUrl = null;
                lastDisconnectReason = null;
                connectedNumber = sock?.user?.id?.split(':')?.[0] || sock?.user?.id || null;
            }

            if (connection === 'close') {
                const reason = new Boom(lastDisconnect?.error)?.output?.statusCode;
                lastDisconnectReason = DisconnectReason[reason] || String(reason || 'unknown');
                connectionStatus = reason === DisconnectReason.loggedOut ? 'logged_out' : 'disconnected';
                connectedNumber = null;
                if (reason !== DisconnectReason.loggedOut) {
                    setTimeout(() => startSocket(), 2500);
                }
            }
        });
    } finally {
        reconnecting = false;
    }
}

app.get('/health', (req, res) => {
    res.json({ success: true, service: 'silapak-wa-gateway', status: connectionStatus });
});

app.get('/status', requireToken, (req, res) => {
    res.json({
        success: true,
        status: connectionStatus,
        connected: connectionStatus === 'connected',
        number: connectedNumber,
        last_disconnect_reason: lastDisconnectReason,
        has_qr: Boolean(qrDataUrl),
    });
});

app.get('/qr', requireToken, (req, res) => {
    res.json({
        success: true,
        status: connectionStatus,
        qr: qrDataUrl,
        qr_text: qrText,
    });
});

app.post('/send-message', requireToken, async (req, res) => {
    try {
        const number = req.body.nomor || req.body.number || req.body.to;
        const message = req.body.pesan || req.body.message || req.body.text;

        if (!number || !message) {
            return res.status(422).json({ success: false, message: 'nomor dan pesan wajib diisi.' });
        }
        if (!sock || connectionStatus !== 'connected') {
            return res.status(503).json({ success: false, message: 'WhatsApp belum terhubung.' });
        }

        const jid = normalizeNumber(number);
        const result = await sock.sendMessage(jid, { text: message });
        return res.json({ success: true, jid, message_id: result?.key?.id || null });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.post('/send-document', requireToken, async (req, res) => {
    try {
        const number = req.body.nomor || req.body.number || req.body.to;
        const caption = req.body.pesan || req.body.caption || req.body.message || '';
        const base64 = req.body.dokumen_base64 || req.body.document_base64 || req.body.base64;
        const fileName = req.body.nama_file || req.body.fileName || 'dokumen.pdf';
        const mimeType = req.body.mime_type || req.body.mimetype || 'application/pdf';

        if (!number || !base64) {
            return res.status(422).json({ success: false, message: 'nomor dan dokumen_base64 wajib diisi.' });
        }
        if (!sock || connectionStatus !== 'connected') {
            return res.status(503).json({ success: false, message: 'WhatsApp belum terhubung.' });
        }

        const buffer = Buffer.from(base64, 'base64');
        if (!buffer.length) {
            return res.status(422).json({ success: false, message: 'dokumen_base64 tidak valid.' });
        }

        const jid = normalizeNumber(number);
        const payload = {
            document: buffer,
            mimetype: mimeType,
            fileName,
        };
        if (caption) {
            payload.caption = caption;
        }

        const result = await sock.sendMessage(jid, payload);
        return res.json({
            success: true,
            jid,
            message_id: result?.key?.id || null,
            file_name: fileName,
            size: buffer.length,
        });
    } catch (error) {
        return res.status(500).json({ success: false, message: error.message });
    }
});

app.post('/restart', requireToken, async (req, res) => {
    await startSocket();
    res.json({ success: true, message: 'Restart koneksi diproses.' });
});

app.post('/logout', requireToken, async (req, res) => {
    try {
        await sock?.logout();
    } catch (error) {
        // ignore logout failures
    }
    connectionStatus = 'logged_out';
    connectedNumber = null;
    qrText = null;
    qrDataUrl = null;
    res.json({ success: true, message: 'WhatsApp logout.' });
});

app.listen(port, async () => {
    console.log(`SILAPAK WhatsApp Gateway running on http://127.0.0.1:${port}`);
    await startSocket();
});
