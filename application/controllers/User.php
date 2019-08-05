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
        //mengambil data dari database
        $id = $this->get('id');
        $hak_akses = '';
        if($id == null || $id == ""){  
            $data = $this->db->get('tb_user');
            foreach($data->result() as $key){
                $this->db->where('id_user',$key->id);
                $full_data = $this->db->get('tb_hak_akses');
               
                $row = $full_data->row();
                if(isset($row)){
                    
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
            $this->db->where('id',$id);
            $data = $this->db->get('tb_user');
            $key = $data->row();
            if(isset($key)){
                $this->db->where('id_user',$key->id);
                $full_data = $this->db->get('tb_hak_akses')->row();
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
        $this->response([ 
            'success' => true,
            'message' => 'API',
            //fungsi result sebagai wadah
            'data'    => $data_auth
        ], 200);
    }
    public function index_post()
    {
        $id         = $this->post("id");
        $nama       = $this->post("nama");
        $nidn       = $this->post("nidn");
        $email      = $this->post("email");
        $password   = $this->post("password");
        $kategori   = $this->post("kategori");
        $data = array(
            "nama"      => $nama,
            "nidn"      => $nidn,
            "email"     => $email,
            "password"  => md5($password),
            "kategori"  => $kategori
        );
        $simpan = $this->db->insert("tb_user",$data);
        if($simpan){
            $this->db->where($data);
            $getdata = $this->db->get("tb_user")->row();

            if(isset($getdata)){
                $data_akses = array(
                    "id_user" => $getdata->id,
                    "status" => "aktif"
                );
                $simpan_hak_akses = $this->db->insert("tb_hak_akses",$data_akses);
                if($simpan_hak_akses){
                    $this->response([
                        'success' => false,
                        'message' => 'User berhasil disimpan tetapi gagal simpan ke hak akses',
                        'data'    => '404'
                    ], 200);
                }else{
                    $this->response([
                        'success' => true,
                        'message' => 'Data Berhasil Dikirim',
                        'data'    => '404'
                    ], 200);
                }
            }
          
        } else {
            $this->response([
                'success' => false,
                'message' => 'Data Gagal Dikirim',
                'data'    => '404'
            ], 200);
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