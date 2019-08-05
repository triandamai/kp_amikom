<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Site extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		
	}

	//Fungsi Get untuk mengambil Data
	public function index_get()
	{
		//mengambil data dari Database
		$data = $this->db->get('tb_admin');
		
		$this->response([
			'success' => true,
			'message' => 'API',
			//fungsi result sebagai wadah
			'mahasiswa' => $data->result()

			//,'artikel' => $data->result()
			
		], 200);
	}

	public function index_post()
	{
		$nama = $this->post("nama");
		$nim = $this->post("nim");
		$kelas = $this->post("kelas");

		$data = array(
			"nama" 	=> $nama,
			"nim" 	=> $nim,
			"kelas" => $kelas 
		);

		$simpan = $this->db->insert("tb_admin",$data);

		if($simpan){
			$this->response([
				'success' => true,
				'message' => 'Data Berhasil Terkirim',
				'data' => '404'
			], 200);
		} else {
			$this->response([
				'success' => false,
				'message' => 'Data Gagal Dikirim',
				'data' => '404'
			], 200);
		}
		
	}

	public function index_put()
	{
		$nim   = $this->put('nim');
		$nama  = $this->put('nama');
		$kelas = $this->put('kelas');
		$data = array (
			'nama'  => $nama,
			'kelas' => $kelas
		);
		$this->db->where('nim',$nim);

		$ubah = $this->db->update('tb_admin',$data);
		if($ubah) {
			$this->response([
				'success' => true,
				'message' => 'Data Berhasil Terkirim',
				'data' => '404'
			], 200);
		} else {
			$this->response([
				'success' => false,
				'message' => 'Data Gagal Dikirim',
				'data' => '404'
			], 200);	
		}
	}

	public function index_delete()
	{
		$nim = $this->query('nim');
		$this->db->where('nim', $nim);
		$hapus = $this->db->delete('tb_admin');
		if($hapus){
			$this->response([
				'success' => true,
				'message' => 'Data Berhasil Terkirim',
				'data' => '404'
			], 200);
		} else {
			$this->response([
				'success' => false,
				'message' => 'Data Gagal Dikirim',
				'data' => '404'
			], 200);
		}
	}
}