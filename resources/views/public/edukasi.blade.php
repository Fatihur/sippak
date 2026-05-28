@extends('layouts.app')
@section('title','Edukasi Kekerasan Perempuan dan Anak')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-4xl font-black text-slate-950">Edukasi dan Jenis Kekerasan</h1>
    <p class="mt-3 max-w-3xl text-slate-600">Kenali bentuk kekerasan, hak korban, dan cara mencari bantuan. Jika Anda atau orang terdekat mengalami kekerasan, jangan ragu untuk melapor.</p>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach([['Kekerasan Fisik','Tindakan yang melukai tubuh atau menyebabkan rasa sakit.'],['Kekerasan Seksual','Pemaksaan, pelecehan, atau tindakan seksual tanpa persetujuan.'],['Kekerasan Verbal','Ucapan mengancam, merendahkan, menghina, atau mempermalukan.'],['KDRT','Kekerasan yang terjadi di dalam relasi rumah tangga.'],['Penelantaran Anak','Pengabaian kebutuhan dasar, pendidikan, kesehatan, atau perlindungan.'],['Eksploitasi Anak','Pemanfaatan anak untuk pekerjaan, ekonomi, atau kepentingan lain.'],['Pelecehan','Perilaku yang membuat korban tidak nyaman dan tidak aman.'],['Cyber Bullying','Perundungan, ancaman, atau pelecehan melalui media digital.']] as [$title,$desc])<article class="rounded-none bg-white p-6 shadow-sm ring-1 ring-slate-200"><h2 class="font-black text-slate-950">{{ $title }}</h2><p class="mt-3 text-sm leading-6 text-slate-600">{{ $desc }}</p></article>@endforeach</div>
</section>
@endsection
