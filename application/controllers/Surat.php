<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
//mengambil dari tabel Database tb_surat
class Surat extends REST_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }

    //fungsi Get untuk mengambil data
    public function index_get()
    {
        //mengambil data dari database
        $id= $this->get('id');
        if($id == null || $id == ''){
            $data = $this->db->get('tb_surat');
        }else{
        $data = $this->db->where('id_user', $id);
        $data = $this->db->get('tb_surat');
        }
        
        //select * from tb_kaprodi
        $this->response([
            'success' => true,
            'message' => 'Daftar Download Surat',
            //fungsi result sebagai wadah
            'data'    => $data->result()
        ], 200);
    }

    public function index_post()
    {
        //untuk bagian ini penamaan bebas
        $judul_surat    = $this->post("judul_surat");
        $jenis_surat    = $this->post("jenis_surat");
        $file_surat     = $this->post("file_surat");
        $id_user        = $this->post("id_user");
        $ext            = $this->post("ext");

		$ext_final = str_replace("application/","",$ext);
		$file_surat = substr(explode(";",$file_surat)[1], 7);
		$new_name = $jenis_surat."_".date("d-m-Y")."_".time().".".$ext_final;
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/kp_amikom/uploads_surat/".$new_name, base64_decode($file_surat));
            //jika berhasil
            $data = array(
                "id"            => $id,
                "judul_surat"   => $judul_surat,   
                "jenis_surat"   => $jenis_surat,
                "tanggal"       => date("Y-m-d"),
                "file_surat"    => base_url().'uploads_surat/'.$new_name,
                "isi_surat"     => $new_name,
                "id_user"       => $id_user
            );
            //simpan ke database
            $simpan = $this->db->insert("tb_surat",$data);

            if($simpan){
                //berhasil simpan
                $this->response([
                    'success' => true,
                    'message' => 'Data Berhasil Terkirim',
                    'data'    => '404'
                ], 200);
            } else {
                //gagal simpan
                $this->response([
                    'success' => false,
                    'message' => 'Data Gagal Dikirim',
                    'data'    => '404'
                ], 200);
            }
        
    }

    public function index_delete()
    {
        $id    = $this->delete('id');
        $surat = $this->db->select('isi_surat');
        $surat = $this->db->where('id', $id);
        $surat = $this->db->get('tb_surat')->row();

        // $this->db->where('id', $id);
        // $hapus = $this->db->delete('tb_surat');
        if($surat != null){
            //fungsi unlink utntuk menghapus file
            unlink("uploads_Surat/" . $surat->isi_surat);
        }
        $hapus = $this->db->where('id', $id);
        $hapus = $this->db->delete('tb_surat');

        if($hapus){
            $this->response([
                'success' => true,
                'message' => 'Data Berhasil Terhapus',
                'data'    => $id
            ], 200);
        } else {
            $this->response([
                'success' => false,
                'message' => 'Data Gagal Dihapus',
                'data'    => '404'
            ], 200);
        }
    }
}