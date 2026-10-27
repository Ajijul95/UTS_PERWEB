
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| Fungsi status usia aset
|--------------------------------------------------------------------------
*/

if (!function_exists('statusUsiaAset')) {
    function statusUsiaAset($tahun)
    {
        return (int) $tahun >= 2023 ? 'Baru' : 'Lama';
    }
}

/*
|--------------------------------------------------------------------------
| Halaman utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/inventaris');
});

/*
|--------------------------------------------------------------------------
| READ, SEARCH, DAN FILTER
|--------------------------------------------------------------------------
*/

Route::get('/inventaris', function (Request $request) {
    try {
        $search = trim((string) $request->query('search', ''));
        $ruangId = (string) $request->query('ruang_id', '');

        $sql = 'SELECT inventaris.*, ruang.nama_ruang
                FROM inventaris
                INNER JOIN ruang ON inventaris.ruang_id = ruang.id';

        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = '(
                inventaris.kode_barang LIKE ?
                OR inventaris.nama_barang LIKE ?
                OR inventaris.nomor_aset LIKE ?
            )';

            $keyword = '%' . $search . '%';
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if ($ruangId !== '' && ctype_digit($ruangId)) {
            $conditions[] = 'inventaris.ruang_id = ?';
            $params[] = (int) $ruangId;
        }

        if (count($conditions) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY inventaris.id';

        $inventaris = DB::select($sql, $params);

        $ruang = DB::select(
            'SELECT id, nama_ruang FROM ruang ORDER BY nama_ruang'
        );

        return view('inventaris.index', compact(
            'inventaris',
            'ruang',
            'search',
            'ruangId'
        ));
    } catch (\Throwable $e) {
        report($e);

        return response(
            'Data inventaris gagal dimuat. Silakan coba lagi.',
            500
        );
    }
})->name('inventaris.index');

/*
|--------------------------------------------------------------------------
| CREATE: TAMBAH INVENTARIS
|--------------------------------------------------------------------------
*/

Route::post('/inventaris', function (Request $request) {
    $data = [
        'kode_barang' => trim(strip_tags(
            (string) $request->input('kode_barang', '')
        )),
        'nama_barang' => trim(strip_tags(
            (string) $request->input('nama_barang', '')
        )),
        'nomor_aset' => trim(strip_tags(
            (string) $request->input('nomor_aset', '')
        )),
        'tahun_perolehan' => $request->input('tahun_perolehan'),
        'ruang_id' => $request->input('ruang_id'),
    ];

    $validator = validator($data, [
        'kode_barang' => [
            'required',
            'regex:/^[A-Za-z0-9]{10}$/',
            'unique:inventaris,kode_barang',
        ],
        'nama_barang' => [
            'required',
            'string',
            'min:3',
            'max:100',
        ],
        'nomor_aset' => [
            'required',
            'string',
            'min:5',
            'max:30',
            'unique:inventaris,nomor_aset',
        ],
        'tahun_perolehan' => [
            'required',
            'integer',
            'min:2000',
            'max:2026',
        ],
        'ruang_id' => [
            'required',
            'integer',
            'exists:ruang,id',
        ],
    ], [
        'kode_barang.regex' =>
            'Kode barang harus tepat 10 karakter alfanumerik.',
        'kode_barang.unique' =>
            'Kode barang sudah digunakan.',
        'nomor_aset.unique' =>
            'Nomor aset sudah digunakan.',
        'tahun_perolehan.min' =>
            'Tahun perolehan minimal 2000.',
        'tahun_perolehan.max' =>
            'Tahun perolehan maksimal 2026.',
    ]);

    if ($validator->fails()) {
        return redirect('/inventaris')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        DB::insert(
            'INSERT INTO inventaris
                (
                    kode_barang,
                    nama_barang,
                    nomor_aset,
                    tahun_perolehan,
                    ruang_id,
                    created_at,
                    updated_at
                )
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $data['kode_barang'],
                $data['nama_barang'],
                $data['nomor_aset'],
                (int) $data['tahun_perolehan'],
                (int) $data['ruang_id'],
            ]
        );

        return redirect('/inventaris')
            ->with('success', 'Data inventaris berhasil ditambahkan.');
    } catch (\Throwable $e) {
        report($e);

        return redirect('/inventaris')
            ->withInput()
            ->with('error', 'Data gagal disimpan. Silakan periksa kembali.');
    }
})->name('inventaris.store');

/*
|--------------------------------------------------------------------------
| FORM EDIT
|--------------------------------------------------------------------------
*/

Route::get('/inventaris/{id}/edit', function ($id) {
    try {
        $item = DB::selectOne(
            'SELECT * FROM inventaris WHERE id = ?',
            [$id]
        );

        if (!$item) {
            abort(404);
        }

        $ruang = DB::select(
            'SELECT id, nama_ruang FROM ruang ORDER BY nama_ruang'
        );

        return view('inventaris.edit', compact('item', 'ruang'));
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (\Throwable $e) {
        report($e);

        return response(
            'Data gagal dimuat. Silakan coba lagi.',
            500
        );
    }
})->whereNumber('id')->name('inventaris.edit');

/*
|--------------------------------------------------------------------------
| UPDATE: SIMPAN PERUBAHAN
|--------------------------------------------------------------------------
*/

Route::put('/inventaris/{id}', function (Request $request, $id) {
    $data = [
        'kode_barang' => trim(strip_tags(
            (string) $request->input('kode_barang', '')
        )),
        'nama_barang' => trim(strip_tags(
            (string) $request->input('nama_barang', '')
        )),
        'nomor_aset' => trim(strip_tags(
            (string) $request->input('nomor_aset', '')
        )),
        'tahun_perolehan' => $request->input('tahun_perolehan'),
        'ruang_id' => $request->input('ruang_id'),
    ];

    $validator = validator($data, [
        'kode_barang' => [
            'required',
            'regex:/^[A-Za-z0-9]{10}$/',
            Rule::unique('inventaris', 'kode_barang')->ignore($id),
        ],
        'nama_barang' => [
            'required',
            'string',
            'min:3',
            'max:100',
        ],
        'nomor_aset' => [
            'required',
            'string',
            'min:5',
            'max:30',
            Rule::unique('inventaris', 'nomor_aset')->ignore($id),
        ],
        'tahun_perolehan' => [
            'required',
            'integer',
            'min:2000',
            'max:2026',
        ],
        'ruang_id' => [
            'required',
            'integer',
            'exists:ruang,id',
        ],
    ], [
        'kode_barang.regex' =>
            'Kode barang harus tepat 10 karakter alfanumerik.',
        'kode_barang.unique' =>
            'Kode barang sudah digunakan barang lain.',
        'nomor_aset.unique' =>
            'Nomor aset sudah digunakan barang lain.',
        'tahun_perolehan.min' =>
            'Tahun perolehan minimal 2000.',
        'tahun_perolehan.max' =>
            'Tahun perolehan maksimal 2026.',
    ]);

    if ($validator->fails()) {
        return redirect('/inventaris/' . $id . '/edit')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $item = DB::selectOne(
            'SELECT id FROM inventaris WHERE id = ?',
            [$id]
        );

        if (!$item) {
            abort(404);
        }

        DB::update(
            'UPDATE inventaris
             SET kode_barang = ?,
                 nama_barang = ?,
                 nomor_aset = ?,
                 tahun_perolehan = ?,
                 ruang_id = ?,
                 updated_at = NOW()
             WHERE id = ?',
            [
                $data['kode_barang'],
                $data['nama_barang'],
                $data['nomor_aset'],
                (int) $data['tahun_perolehan'],
                (int) $data['ruang_id'],
                $id,
            ]
        );

        return redirect('/inventaris')
            ->with('success', 'Data inventaris berhasil diperbarui.');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (\Throwable $e) {
        report($e);

        return redirect('/inventaris/' . $id . '/edit')
            ->withInput()
            ->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
    }
})->whereNumber('id')->name('inventaris.update');

/*
|--------------------------------------------------------------------------
| DELETE: HAPUS INVENTARIS
|--------------------------------------------------------------------------
*/

Route::delete('/inventaris/{id}', function ($id) {
    try {
        $item = DB::selectOne(
            'SELECT id FROM inventaris WHERE id = ?',
            [$id]
        );

        if (!$item) {
            abort(404);
        }

        DB::delete(
            'DELETE FROM inventaris WHERE id = ?',
            [$id]
        );

        return redirect('/inventaris')
            ->with('success', 'Data inventaris berhasil dihapus.');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (\Throwable $e) {
        report($e);

        return redirect('/inventaris')
            ->with('error', 'Data gagal dihapus. Silakan coba lagi.');
    }
})->whereNumber('id')->name('inventaris.destroy');
