
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventaris</title>
</head>
<body>
    <h1>Edit Data Inventaris</h1>

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

    <form
        action="{{ route('inventaris.update', $item->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <p>
            <label for="kode_barang">Kode Barang</label><br>
            <input
                type="text"
                id="kode_barang"
                name="kode_barang"
                maxlength="10"
                value="{{ old('kode_barang', $item->kode_barang) }}"
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
                value="{{ old('nama_barang', $item->nama_barang) }}"
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
                value="{{ old('nomor_aset', $item->nomor_aset) }}"
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
                value="{{ old('tahun_perolehan', $item->tahun_perolehan) }}"
                required
            >
        </p>

        <p>
            <label for="ruang_id">Ruang</label><br>
            <select id="ruang_id" name="ruang_id" required>
                @foreach ($ruang as $r)
                    <option
                        value="{{ $r->id }}"
                        {{ (string) old('ruang_id', $item->ruang_id) === (string) $r->id ? 'selected' : '' }}
                    >
                        {{ $r->nama_ruang }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ url('/inventaris') }}">Kembali</a>
    </form>
</body>
</html>
