<?php

class Transaksi_model extends CI_Model {
    public function AmbilLaporan($tanggal)
    {
        $tglawal = $tanggal['tglawal'];
        $tglakhir = $tanggal['tglakhir'];
        // $query = $this->db->query("SELECT
        //     `transaksi`.*,
        //     `pemesanan`.`kd_pemesanan`,
        //     `pemesanan`.`tgl_pemesanan`,
        //     `pemesanan`.`status`,
        //     `pelanggan`.`kd_pelanggan`,
        //     `pelanggan`.`nama`,
        //     `pelanggan`.`alamat`,
        //     `pelanggan`.`no_hp`,
        //     `pelanggan`.`jk`,
        //     `detail`.`berat`,
        //     `detail`.`jumlah`,
        //     `detail`.`bayar`,
        //     `jenispakaian`.`jenis`,
        //     `jenispakaian`.`harga`,
        //     `jenispakaian`.`statusbiaya`
        // FROM
        //     `transaksi`
        //     LEFT JOIN `pemesanan` ON `pemesanan`.`id` = `transaksi`.`id_pemesanan`
        //     LEFT JOIN `pelanggan` ON `pemesanan`.`kd_pelanggan` =
        // `pelanggan`.`kd_pelanggan`
        //     LEFT JOIN `detail` ON `transaksi`.`kd_transaksi` = `detail`.`kd_transaksi`
        //     LEFT JOIN `jenispakaian` ON `detail`.`idjenispakaian` =
        // `jenispakaian`.`idjenispakaian`
        // WHERE tgl_ambil >= '$tglawal' AND tgl_ambil<='$tglakhir'");
        // return $query->result();
        $query = $this->db->query("SELECT
            `pemesanan`.`kd_pemesanan`,
            `pemesanan`.`tgl_pemesanan`,
            `pemesanan`.`kd_pelanggan`,
            `pemesanan`.`status`,
            `transaksi`.`id_pemesanan`,
            `transaksi`.`kd_transaksi`,
            `transaksi`.`kd_pegawai`,
            `transaksi`.`tgl_ambil`,
            `transaksi`.`total`,
            `pelanggan`.`nama`,
            `pelanggan`.`kd_pelanggan` AS `kd_pelanggan1`,
            `pelanggan`.`alamat`,
            `pelanggan`.`no_hp`,
            `pelanggan`.`jk`,
            `pelanggan`.`iduser`
        FROM
            `transaksi`
            LEFT JOIN `pemesanan` ON `pemesanan`.`id` = `transaksi`.`id_pemesanan`
            LEFT JOIN `pelanggan` ON `pelanggan`.`kd_pelanggan` =
            `pemesanan`.`kd_pelanggan`
        WHERE tgl_ambil >= '$tglawal' AND tgl_ambil<='$tglakhir'");
        $transaksi = $query->result();
        foreach ($transaksi as $key => $value) {
            $detail = $this->db->get_where('detail', array('kd_transaksi'=>$value->kd_transaksi));
            $value->detail = $detail->result();
        }
        return $transaksi;
    }
    function select()
    {
        $data = ['transaksi'=>array(), 'pemesanan'=>array(), 'jenis' =>array()];
        $query = $this->db->query("SELECT
            `pemesanan`.`kd_pemesanan`,
            `pemesanan`.`tgl_pemesanan`,
            `pemesanan`.`kd_pelanggan`,
            `pemesanan`.`status`,
            `transaksi`.`id_pemesanan`,
            `transaksi`.`kd_transaksi`,
            `transaksi`.`kd_pegawai`,
            `transaksi`.`tgl_ambil`,
            `transaksi`.`total`,
            `pelanggan`.`nama`,
            `pelanggan`.`kd_pelanggan` AS `kd_pelanggan1`,
            `pelanggan`.`alamat`,
            `pelanggan`.`no_hp`,
            `pelanggan`.`jk`,
            `pelanggan`.`iduser`
        FROM
            `transaksi`
            LEFT JOIN `pemesanan` ON `pemesanan`.`id` = `transaksi`.`id_pemesanan`
            LEFT JOIN `pelanggan` ON `pelanggan`.`kd_pelanggan` =
            `pemesanan`.`kd_pelanggan`");
        $transaksi = $query->result();
        foreach ($transaksi as $key => $value) {
            $query= $this->db->query("SELECT
                `detail`.*,
                `jenispakaian`.`jenis`,
                `jenispakaian`.`harga`,
                `jenispakaian`.`statusbiaya`
            FROM
                `detail`
                LEFT JOIN `jenispakaian` ON `detail`.`idjenispakaian` =
            `jenispakaian`.`idjenispakaian` WHERE kd_transaksi='$value->kd_transaksi'");
            $value->jenis = $query->result();
        }
        $data['transaksi']= $transaksi;

        $query= $this->db->query("SELECT
            `pemesanan`.*
        FROM
            `pemesanan`
            LEFT JOIN `transaksi` ON `transaksi`.`id_pemesanan` = `pemesanan`.`id`
        WHERE
            `transaksi`.`id_pemesanan` IS NULL AND pemesanan.status NOT IN('Selesai','Batal')");
        $data['pemesanan']= $query->result();
        $query= $this->db->get('jenispakaian');
        $data['jenis']= $query->result();
        return $data;
    }

    public function insert($data)
    {
        $itemtrans = [
            'id_pemesanan'=>$data['id_pemesanan'],
            'kd_pegawai'=>$this->session->userdata('kd_pegawai'),
            'tgl_ambil'=>$data['tgl_ambil'],
            'total'=>$data['total'],
        ];
        $itempem = [
            'status'=>'Selesai'
        ];
        $this->db->trans_begin();

        $this->db->insert('transaksi', $itemtrans);
        $kd_transaksi = $this->db->insert_id();
        foreach ($data['jenis'] as $key => $value) {
            $itemdetail = [
                'idjenispakaian'=>$value['idjenispakaian'],
                'kd_transaksi'=>$kd_transaksi,
                'berat'=>$value['berat'],
                'jumlah'=>$value['jumlah'],
                'bayar'=>$value['bayar']
            ];
            $this->db->insert('detail', $itemdetail);
        }

        $this->db->where('id', $data['id_pemesanan']);
        $this->db->update('pemesanan', $itempem);
        if($this->db->trans_status()==true){
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
            return false;
        }
    }

    public function update($data)
    {
        $itemtrans = [
            'id_pemesanan'=>$data['id_pemesanan'],
            'kd_pegawai'=>$this->session->userdata('kd_pegawai'),
            'tgl_ambil'=>$data['tgl_ambil'],
            'total'=>$data['total'],
        ];
        $this->db->where('kd_transaksi', $data['kd_transaksi']);
        $result = $this->db->update('transaksi', $itemtrans);
        return $result;    
    }
    public function delete($kd_transaksi)
    {
        $this->db->where('kd_transaksi', $kd_transaksi);
        $result = $this->db->delete('transaksi');
        return $result;
    }
}