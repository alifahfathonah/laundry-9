<?php

defined('BASEPATH') or exit('No direct script access allowed');

class laporan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/Transaksi_model', 'TransaksiModel');
    }

    public function index()
    {
        $title['title'] = ['header'=>'Laporan', 'dash'=>'Laporan'];
        $data = $this->TransaksiModel->select();
        $this->load->view('admin/template/header', $title);
        $this->load->view('admin/laporan', $data);
        $this->load->view('admin/template/footer');
    }
    public function getprint()
    {
        $data = $this->input->post();
        $result = $this->TransaksiModel->AmbilLaporan($data);
        echo json_encode($result);
    }
}

/* End of file Controllername.php */