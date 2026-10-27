
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Laboratorium</title>
</head>
<body>
    <h1>Sistem Inventaris Laboratorium</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <h3>Terjadi kesalahan input:</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <hr>

    <h2>Tambah Inventaris</h2>

    <form action="{{ route('inventaris.store') }}" method="POST">
        @csrf

        <p>
            <label for="kode_barang">Kode Barang</label><br>
            <input
                type="text"
                id="kode_barang"
                name="kode_barang"
                maxlength="10"
                value="{{ old('kode_barang') }}"
                required
            >
        </p>

        <p>
            <label for="nama_barang">Nama Barang</label><br>
            <input
                type="text"
                id="nama_barang"
                name="nama_barang"
                minlength="3"
                maxlength="100"
                value="{{ old('nama_barang') }}"
                required
            >
        </p>

        <p>
            <label for="nomor_aset">Nomor Aset</label><br>
            <input
                type="text"
                id="nomor_aset"
                name="nomor_aset"
                minlength="5"
                maxlength="30"
                value="{{ old('nomor_aset') }}"
                required
            >
        </p>

        <p>
            <label for="tahun_perolehan">Tahun Perolehan</label><br>
            <input
                type="number"
                id="tahun_perolehan"
                name="tahun_perolehan"
                min="2000"
                max="2026"
                value="{{ old('tahun_perolehan') }}"
                required
            >
        </p>

        <p>
            <label for="ruang_id">Ruang</label><br>
            <select id="ruang_id" name="ruang_id" required>
                <option value="">-- Pilih Ruang --</option>

                @foreach ($ruang as $r)
                    <option
                        value="{{ $r->id }}"
                        {{ (string) old('ruang_id') === (string) $r->id ? 'selected' : '' }}
                    >
                        {{ $r->nama_ruang }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Barang</button>
    </form>

    <hr>

    <h2>Pencarian dan Filter</h2>

    <form action="{{ url('/inventaris') }}" method="GET">
        <p>
            <label for="search">Cari Barang</label><br>
            <input
                type="text"
                id="search"
                name="search"
                value="{{ $search }}"
                placeholder="Kode, nama, atau nomor aset"
            >
        </p>

        <p>
            <label for="filter_ruang">Filter Ruang</label><br>
            <select id="filter_ruang" name="ruang_id">
                <option value="">Semua Ruang</option>

                @foreach ($ruang as $r)
                    <option
                        value="{{ $r->id }}"
                        {{ $ruangId === (string) $r->id ? 'selected' : '' }}
                    >
                        {{ $r->nama_ruang }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Cari</button>
        <a href="{{ url('/inventaris') }}">Reset</a>
    </form>

    <hr>

    <h2>Daftar Inventaris</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Nomor Aset</th>
                <th>Tahun Perolehan</th>
                <th>Ruang</th>
                <th>Status Usia Aset</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($inventaris as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->nomor_aset }}</td>
                    <td>{{ $item->tahun_perolehan }}</td>
                    <td>{{ $item->nama_ruang }}</td>

                    <td>
                        @if (statusUsiaAset($item->tahun_perolehan) === 'Baru')
                            Baru
                        @else
                            Lama
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('inventaris.edit', $item->id) }}">
                            Edit
                        </a>

                        <form
                            action="{{ route('inventaris.destroy', $item->id) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Data inventaris tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
