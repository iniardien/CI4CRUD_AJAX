<?php

namespace App\Controllers;

use App\Models\ModelPegawai;

class Pegawai extends BaseController
{
    protected $ModelPegawai;
    function __construct()
    {
        $this->ModelPegawai = new ModelPegawai();
    }
    public function hapus($id)
    {
        $this->ModelPegawai->delete($id);
        return redirect()->to('pegawai');
    }
    public function edit($id)
    {
        return json_encode($this->ModelPegawai->find($id));
    }

    public function index()
    {
        $katakunci = $this->request->getGet('katakunci');
        if ($katakunci) {
            $pencarian = $this->ModelPegawai->cari($katakunci);
        } else {
            $pencarian = $this->ModelPegawai;
        }
        $data['katakunci'] = $katakunci;
        $data['dataPegawai'] = $pencarian->orderBy('id', 'desc')->paginate(5);
        $data['pager'] = $this->ModelPegawai->pager;
        $data['nomor'] = $this->request->getVar('page') ? $this->request->getVar('page') : 1;
        return view('pegawai_view', $data);
    }
    public function simpan()
    {
        $validasi = \Config\Services::validation();
        $aturan = [
            'nama' => [
                'label' => 'Nama',
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => '{field} Harus Diisi',
                    'min_length' => 'Minimum Karakter untuk field {field} adalah 5 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|min_length[5]|valid_email',
                'errors' => [
                    'required' => '{field} Harus Diisi',
                    'min_length' => 'Minimum Karakter untuk field {field} adalah 5 karakter',
                    'valid_email' => 'email yang kamu masukan tidak valid'
                ]
            ],
            'alamat' => [
                'label' => 'Alamat',
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => '{field} Harus Diisi',
                    'min_length' => 'Minimum Karakter untuk field {field} adalah 5 karakter'
                ]
            ],
        ];

        $validasi->setRules($aturan);
        if ($validasi->withRequest($this->request)->run()) {
            $id = $this->request->getPost('id');
            $nama = $this->request->getPost('nama');
            $email = $this->request->getPost('email');
            $bidang = $this->request->getPost('bidang');
            $alamat = $this->request->getPost('alamat');

            $data = [
                'id' => $id,
                'nama' => $nama,
                'email' => $email,
                'bidang' => $bidang,
                'alamat' => $alamat
            ];

            $this->ModelPegawai->save($data);

            $hasil['sukses'] = "Berhasil Memasukan Data";
            $hasil['error'] = false;
        } else {
            $hasil['sukses'] = false;
            $hasil['error'] = $validasi->listErrors();
        }

        return json_encode($hasil);
    }
}
