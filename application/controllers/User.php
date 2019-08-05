<?php
defined('BASEPATH') OR exit ('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
//mengambil dari tabel Database tb_user
class User extends REST_Controller {
    public function __construct()
    {
        parent::__construct();
    }
    //Fungsi Get untuk mengambil data
    public function index_get()
    {
       //mngambil data post id
        $id = $this->get('id');
        $hak_akses = '';
        //cek id kosong ? jika kosong ambil dengan where
        if($id == null || $id == ""){  
            //ambil data
            $data = $this->db->get('tb_user');

            //data di ulang untuk mengambil detail hak_akses
            foreach($data->result() as $key){
                //ambil data hak akses
                $this->db->where('id_user',$key->id);
                $full_data = $this->db->get('tb_hak_akses');
               
                //ambil satu data dan buat array baru
                $row = $full_data->row();
                if(isset($row)){
                    //array baru untuk dikirim response
                    $data_auth[] = array(
                        'id' => $key->id,
                        'nama' => $key->nama,
                        'nidn' => $key->nidn,
                        'email' => $key->email,
                        'password'=> $key->password,
                        'kategori' => $key->kategori,
                        'hak_akses' => $row->status
                    );
                }
                
                   
               
            }
        }else{
            //jika id tidak kosong ambil user kondisi id
            $this->db->where('id',$id);
            $data = $this->db->get('tb_user');
            //ambil satu data
            $key = $data->row();
            if(isset($key)){
                //ambil data hak akses
                $this->db->where('id_user',$key->id);
                $full_data = $this->db->get('tb_hak_akses')->row();

                //buat ulang array untuk dikirim sebagai response
                $data_auth[] = array(
                    'id' => $key->id,
                    'nama' => $key->nama,
                    'nidn' => $key->nidn,
                    'email' => $key->email,
                    'password'=> $key->password,
                    'kategori' => $key->kategori,
                    'hak_akses' => $full_data->status
                );
            }
            }   

            //response yang dikirim ke front end (json)
        $this->response([ 
            'success' => true,
            'message' => 'API',
            //fungsi result sebagai wadah
            'data'    => $data_auth
        ], 200);
    }
    public function index_post()
    {
        //simpan data user baru
        //tangkap data dari front end
        $id         = $this->post("id");
        $nama       = $this->post("nama");
        $nidn       = $this->post("nidn");
        $email      = $this->post("email");
        $password   = $this->post("password");
        $kategori   = $this->post("kategori");

        //data dibuat jadi array
        $data = array(
            "nama"      => $nama,
            "nidn"      => $nidn,
            "email"     => $email,
            "password"  => md5($password),
            "kategori"  => $kategori
        );
        //cek sudah ada email ?
        $cek_email = $this->db->where('email',$email);
        $cek_email = $this->db->get('tb_user')->num_rows();
        if($cek_email >0){
            $this->response([
                'success' => false,
                'message' => 'Email Sudah ada',
                'data'    => '404'
            ], 200);
        }else{  
            
        //kemudian data disimpan
        $simpan = $this->db->insert("tb_user",$data);
        //jika berhasil
        if($simpan){
            //data diambil kembali untuk menyimpan ke tb_hak_akses
            $this->db->where($data);
            $getdata = $this->db->get("tb_user")->row();
            
            if(isset($getdata)){
                //data yang akan disimpan ke hak_akses
                $data_akses = array(
                    "id_user" => $getdata->id,
                    "status" => "aktif"
                );
                //proses simpan tb_hak_akses
                $simpan_hak_akses = $this->db->insert("tb_hak_akses",$data_akses);

                //jika berhasil
                if($simpan_hak_akses){
                    $this->response([
                        'success' => false,
                        'message' => 'User berhasil disimpan tetapi gagal simpan ke hak akses',
                        'data'    => '404'
                    ], 200);
                //jika gagal
                }else{
                    $this->response([
                        'success' => false,
                        'message' => 'Berhasil Menyimpan User gagal menyimpan hak_akses',
                        'data'    => '404'
                    ], 200);
                }
            }
          
        } else {
            $this->response([
                'success' => false,
                'message' => 'Gagal Menyimpan User',
                'data'    => '404'
            ], 200);
        }
        }

    }
    public function index_put()
    {
        $id         = $this->put("id");
        $nama       = $this->put("nama");
        $nidn       = $this->put("nidn");
        $email      = $this->put("email");
        $password   = $this->put("password");
        $kategori   = $this->put("kategori");
        $data       = array (
            'nama'      => $nama,
            'nidn'      => $nidn,
            'email'     => $email,
            'password'  => md5($password),
            'kategori'  => $kategori
        );
        $this->db->where('id', $id);
        $ubah = $this->db->update('tb_user',$data);
        if($ubah) {
            $this->response([
                'success' => true,
                'message' => 'Data Berhasil Terkirim',
                'data'    => '404'
            ], 200);
        } else {
            $this->response([
                'success' => false,
                'message' => 'Data Gagal Dikirim',
                'data'    => '404'
            ], 200);
        }
    }
}