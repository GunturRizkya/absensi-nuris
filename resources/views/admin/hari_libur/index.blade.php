@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Judul halaman --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Hari Libur</h1>
        <p class="mt-1 text-sm text-slate-500">
            Kelola hari libur nasional dan libur sekolah.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Periksa kembali data yang dimasukkan:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-slate-800">
            Tambah Hari Libur
        </h2>

        <form action="{{ route('admin.hari-libur.store') }}"
              method="POST"
              class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Nama Hari Libur
                </label>
                <input type="text"
                       name="nama_libur"
                       value="{{ old('nama_libur') }}"
                       required
                       placeholder="Contoh: Libur Hari Raya"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Keterangan
                </label>
                <input type="text"
                       name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Opsional"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Tanggal Mulai
                </label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ old('tanggal_mulai') }}"
                       required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Tanggal Selesai
                </label>
                <input type="date"
                       name="tanggal_selesai"
                       value="{{ old('tanggal_selesai') }}"
                       required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    + Tambah Hari Libur
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-semibold text-slate-800">Daftar Hari Libur</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No.</th>
                        <th class="px-5 py-3">Nama Hari Libur</th>
                        <th class="px-5 py-3">Tanggal Mulai</th>
                        <th class="px-5 py-3">Tanggal Selesai</th>
                        <th class="px-5 py-3">Keterangan</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($hariLiburs as $libur)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                {{ $hariLiburs->firstItem() + $loop->index }}
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-800">
                                {{ $libur->nama_libur }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ $libur->tanggal_mulai->format('d-m-Y') }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ $libur->tanggal_selesai->format('d-m-Y') }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $libur->keterangan ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <button type="button"
                                        onclick="document.getElementById('edit-{{ $libur->id }}').classList.toggle('hidden')"
                                        class="mr-2 rounded-md bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200">
                                    Edit
                                </button>

                                <form action="{{ route('admin.hari-libur.destroy', $libur->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr id="edit-{{ $libur->id }}" class="hidden bg-blue-50">
                            <td colspan="6" class="px-5 py-5">
                                <form action="{{ route('admin.hari-libur.update', $libur->id) }}"
                                      method="POST"
                                      class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Nama Hari Libur
                                        </label>
                                        <input type="text"
                                               name="nama_libur"
                                               value="{{ $libur->nama_libur }}"
                                               required
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Keterangan
                                        </label>
                                        <input type="text"
                                               name="keterangan"
                                               value="{{ $libur->keterangan }}"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Tanggal Mulai
                                        </label>
                                        <input type="date"
                                               name="tanggal_mulai"
                                               value="{{ $libur->tanggal_mulai->format('Y-m-d') }}"
                                               required
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-slate-600">
                                            Tanggal Selesai
                                        </label>
                                        <input type="date"
                                               name="tanggal_selesai"
                                               value="{{ $libur->tanggal_selesai->format('Y-m-d') }}"
                                               required
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div class="flex gap-2 md:col-span-2">
                                        <button type="submit"
                                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                            Simpan Perubahan
                                        </button>
                                        <button type="button"
                                                onclick="document.getElementById('edit-{{ $libur->id }}').classList.add('hidden')"
                                                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                Belum ada data hari libur. Silakan tambahkan data di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-5 py-4">
            {{ $hariLiburs->links() }}
        </div>
    </div>
</div>
@endsection