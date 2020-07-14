<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Data Pelanggan</h3>
            </div>
            <div class="card-body">
                <div class="col-sm-5 d-flex justify-content-between">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="bagian" class="col-sm-3 col-form-label">Tanggal</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control float-right" id="reservation">
                                    <button class="btn btn-primary caridata"
                                        data-Url="<?= base_url()?>admin/laporan/getprint"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="d-flex justify-content-start">
                            <button class="btn btn-primary" id="cetak" style="margin-right:12px;">Cetak</button>
                            <div id="tombolPdf"></div>
                            <!-- <button class="btn btn-primary pdfconvert" target="_blank"
                                data-Url="<?= base_url()?>admin/laporan/CetakPDF">PDF</button> -->
                        </div>
                    </div>
                </div>
                <div id="data-print">
                  <div class="print-header">
                    <center>
                    <h3>LAPORAN TRANSAKSI</h3>
                      <h4>Tanggal <span id="tgllaporan"></span> </h4>
                    </center>
                  </div>
                  <table id="example1" class="table table-bordered" id="konten">
                      <thead>
                          <tr>
                              <th style="width: 10px">No</th>
                              <th class="text-center">Kode Pemesanan</th>
                              <th class="text-center">Nama</th>
                              <th class="text-center">Alamat</th>
                              <th class="text-center">Tanggal Ambil</th>
                              <th class="text-center">Jenis</th>
                              <th class="text-center">Berat (Kg)</th>
                              <th class="text-center">Jumlah</th>
                          </tr>
                      </thead>
                      <tbody id="tblBykeLists">
                          <?php
                        $no =1;
                        foreach($transaksi as $item):?>
                          <tr>
                              <td><?= $no?></td>
                              <td><?= $item->kd_pemesanan?></td>
                              <td><?= $item->nama?></td>
                              <td><?= $item->alamat?></td>
                              <td><?= $item->tgl_ambil?></td>
                              <td><?= $item->jenis_type?></td>
                              <td><?= $item->berat?></td>
                              <td><?= $item->jumlah?></td>
                          </tr>
                          <?php 
                          $no ++;
                        endforeach;
                        ?>
                      </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var doc = new jsPDF();
    $('#konvert').click(function () {   
        doc.fromHTML($('#example1').html(), 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.save('contoh-file.pdf');
    });
    $(document).ready(function() {
        // const tgl1 = $("#reservation").val();
        // const Url1 = $(this).data('url');
        // var tag = Url1 + tgl1;
        // var html = '<button class="btn btn-primary pdfconvert" target="_blank"
        // data - Url = "<?= base_url()?>admin/laporan/CetakPDF" > PDF < /button>'
        // data - Url = "<?= base_url()?>admin/laporan/CetakPDF" > PDF < /button>
        // var a = tgl.split(' - ');
        // var dataa = {
        //     'tglawal': a[0],
        //     'tglakhir': a[1]
        // };
        // $("#tblBykeLists").append(html);
        var tgl1 = $("#reservation").val();
            var a = tgl1.split(' - ');
            var dataa = {
                'tglawal': a[0],
                'tglakhir': a[1]
            };
        var datatanggal = "Dari Tanggal " + convertanggal(a[0]) + " s/d " + convertanggal(a[0]);
        $("#tgllaporan").text(datatanggal);

        bsCustomFileInput.init();
    });
    $(document).ready(function() {
        var tombol;
        var kd_transaksi;
        var tgl_ambil;
        var jenis_type;
        var berat;
        var jumlah;
        var dataa;
        // get Edit Product
        $('.caridata').on('click', function() {
            $('#tblBykeLists').children('tr').remove();
            const tgl = $("#reservation").val();
            const Url = $(this).data('url');
            var a = tgl.split(' - ');
            dataa = {
                'tglawal': a[0],
                'tglakhir': a[1]
            };
            $.ajax({
                type: 'POST',
                url: Url,
                data: dataa,
                success: function(data) {
                  if(data == '[]'){
                    swal("Data tidak di temukan", "");
                  }
                    $data = JSON.parse(data);
                    $.each($data, function(index, value) {
                        var html = "<tr><td>" + Number(1 + index) +
                            "</td><td>" + value.kd_pemesanan + "</td><td>" + value.nama + "</td><td>" + value.alamat + "</td><td>" +
                            value.tgl_ambil + "</td><td>" + value
                            .jenis_type + "</td><td>" + value.berat +
                            "</td><td>" + value.jumlah + "</td></tr>";
                        $("#tblBykeLists").append(html);
                    });

                }
            });
        });

        $('.pdfconvert').on('click', function() {
            const tgl = $("#reservation").val();
            const Url = $(this).data('url');
            var a = tgl.split(' - ');
            var dataa = {
                'tglawal': a[0],
                'tglakhir': a[1]
            };
            $.ajax({
                type: 'POST',
                url: Url,
                data: dataa,
                success: function(data) {
                    console.log();
                }
            });
        });


        $('.ubahTransaksi').on('click', function() {
            // get data from button edit
            kd_transaksi = $(this).data('kd_transaksi');
            tgl_ambil = $(this).data('tgl_ambil');
            jenis_type = $(this).data('jenis_type');
            berat = $(this).data('berat');
            jumlah = $(this).data('jumlah');
            // Set data to Form Edit
            $('#kd_transaksi').val(kd_transaksi);
            $('#tgl_ambil').val(tgl_ambil);
            $('#jenis_type').val(jenis_type);
            $('#berat').val(berat);
            $('#jumlah').val(jumlah);
            $('.prosess').val('Ubah');
        });

        $('.clear').on('click', function() {
            $('#kd_transaksi').val("");
            $('#tgl_ambil').val("");
            $('#jenis_type').val("");
            $('#berat').val("");
            $('#jumlah').val("");
            $('.prosess').val('Simpan');
            $("#kd_pemesanan").show();
        });

        // get Delete Product
        $('.hapuspelanggan').on('click', function() {
            // get data from button edit
            const Url = $(this).data('url');
            // Set data to Form Edit
            // $('.edit-kategori').val(idkategori);
            swal({
                    title: "Anda Yakin?",
                    text: "Akan Melakukan Penghapusan data?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            url: Url,
                            success: function(data) {
                                swal("Information!", "Berhasil di Hapus",
                                        "success")
                                    .then((value) => {
                                        location.reload();
                                    });
                            }
                        });
                    }
                });
        });
    });
})
$(function() {
    $("#example1").DataTable({
        "responsive": true,
        "autoWidth": false,
    });
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });
});
</script>