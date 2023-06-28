<?php

$params = $request->getParams();
    $db     = Db::db();
    
    //data Gambar
    $dataGambar = $db->select('*')
        ->from('m_setting');

    $modelGbr = $dataGambar->find();

    if (isset($params['id']) && !empty($params['id']) && $params['id'] != 'undefined') {

        $models = $db->select('t_pembelian_insentif.*,
                m_tambak.id as id_tambak,
                m_tambak.nama as nama_tambak,
                m_tambak.kode as kode_tambak,
                m_status.nama as nama_status,
                m_petani.no_npwp as npwp_petani,
                m_petani.nama as nama_petani,
                m_spesies.nama as nama_spesies,
                m_spesies.id as spesies_id,
                m_rm.id as nama_rm,
                m_rm.nama as rm,
                m_supplier.id as m_supplier_id,
                m_supplier.kode as kode_sup,
                m_supplier.nama as nama_supplier,
                m_supplier.npwp as npwp_supplier,
                m_jadwal_det.kode as kode_jadwal,
                m_jadwal_det.tanggal_mulai as tanggal_mulai_jadwal,
                m_jadwal_det.tanggal_selesai as tanggal_selesai_jadwal,
                wilayah_kabupaten.id as id_kabupaten,
                m_user.nama as nama_pembuat,
                approve.nama as nama_approve,
                m_jabatan.nama as jabatan_pembuat,
                jabatan_approve.nama as approve_jabatan
                      ')
            ->from('t_pembelian_insentif')
            ->leftjoin('m_tambak', 't_pembelian_insentif.m_tambak_id = m_tambak.id')
            ->leftjoin('m_petani', 'm_tambak.m_petani_id = m_petani.id')
            ->leftjoin('wilayah_kabupaten', 'm_tambak.kabupaten_id = wilayah_kabupaten.id')
            ->leftjoin('m_status', 't_pembelian_insentif.m_status_id = m_status.id')
            ->leftjoin('m_supplier', 't_pembelian_insentif.m_supplier_id = m_supplier.id')
            ->leftjoin('m_spesies', 't_pembelian_insentif.m_spesies_id = m_spesies.id')
            ->leftjoin('m_jadwal_det', 't_pembelian_insentif.penerimaan_order_id = m_jadwal_det.id')
            ->leftjoin('m_user', 't_pembelian_insentif.created_by = m_user.id')
            ->leftjoin('m_user as approve', 't_pembelian_insentif.user_approve_id = approve.id')
            ->leftjoin('m_jabatan', 'm_user.m_jabatan_id = m_jabatan.id')
            ->leftjoin('m_jabatan as jabatan_approve', 'approve.m_jabatan_id = m_jabatan.id')
            ->leftjoin('m_rm', 't_pembelian_insentif.m_rm_id = m_rm.id')
            ->where('t_pembelian_insentif.id', '=', $params['id'])
            ->find();




        $detail = $db->select('
          t_pembelian_insentif_det.*,SUM(t_pembelian_insentif_det.kuantiti) as kuantiti ,
          m_size.nama as nama_size,
          m_size_kategori.nama as size_kategori,
          m_grade.nama as grade
      ')
            ->from('t_pembelian_insentif_det')
            ->innerJoin('m_size', 't_pembelian_insentif_det.m_size_id = m_size.id')
            ->leftjoin('m_size_kategori', 'm_size.m_size_kategori_id = m_size_kategori.id')
            ->leftjoin('m_grade', 't_pembelian_insentif_det.m_grade_id = m_grade.id')
            ->where('t_pembelian_insentif_det.t_pembelian_insentif_id', '=', $params['id'])
            ->groupBy('t_pembelian_insentif_det.m_size_id, t_pembelian_insentif_det.m_grade_id')
            ->findAll();
    } else {


        $data = $db->select('t_penerimaan.*,
                m_tambak.id as id_tambak,
                m_tambak.nama as nama_tambak,
                m_tambak.kode as kode_tambak,
                m_status.nama as nama_status,
                m_petani.no_npwp as npwp_petani,
                m_petani.nama as nama_petani,
                m_spesies.nama as nama_spesies,
                m_spesies.id as spesies_id,
                m_rm.id as nama_rm,
                m_rm.nama as rm,
                m_supplier.id as m_supplier_id,
                m_supplier.kode as kode_sup,
                m_supplier.nama as nama_supplier,
                m_supplier.npwp as npwp_supplier,
                m_jadwal_det.kode as kode_jadwal,
                m_jadwal_det.tanggal_mulai as tanggal_mulai_jadwal,
                m_jadwal_det.tanggal_selesai as tanggal_selesai_jadwal,
                wilayah_kabupaten.id as id_kabupaten
                      ')
            ->from('t_penerimaan')
            ->leftjoin('m_tambak', 't_penerimaan.m_tambak_id = m_tambak.id')
            ->leftjoin('m_petani', 'm_tambak.m_petani_id = m_petani.id')
            ->leftjoin('wilayah_kabupaten', 'm_tambak.kabupaten_id = wilayah_kabupaten.id')
            ->leftjoin('m_status', 't_penerimaan.status = m_status.id')
            ->leftjoin('m_supplier', 't_penerimaan.supplier_id = m_supplier.id')
            ->leftjoin('m_spesies', 't_penerimaan.m_spesies_id = m_spesies.id')
            ->leftjoin('m_jadwal_det', 't_penerimaan.penerimaan_order = m_jadwal_det.id')
            ->leftjoin('m_rm', 't_penerimaan.asal_rm = m_rm.id')
            ->where('t_penerimaan.tipe', '=', 'luar');


        if (isset($params['m_tambak_id']) && $params['m_tambak_id'] != 'undefined' && $params['m_tambak_id'] != "") {
            $db->where('t_penerimaan.m_tambak_id', '=', $params['m_tambak_id']);
        } else {
            $db->where('t_penerimaan.supplier_id', '=', $params['supplier_id']);
        }
        if (!empty($params['status'])) {
            $db->where('t_penerimaan.status', '=', $params['status']);
        }
        if (!empty($params['jenis_udang'])) {
            $db->where('t_penerimaan.m_spesies_id', '=', $params['jenis_udang']);
        }
        if (!empty($params['jenis_rm'])) {
            $db->where('t_penerimaan.asal_rm', '=', $params['jenis_rm']);
        }
        if (!empty($params['tanggal'])) {
            $params['tanggal'] = strtotime($params['tanggal']);
            $newformat = date('Y-m-d', $params['tanggal']);

            $db->where('t_penerimaan.tanggal', '=', $newformat);
        }
        if (!empty($params['metode_pengiriman'])) {
            $db->where('t_penerimaan.pengiriman_id', '=', $params['metode_pengiriman']);
        }

        $models = $data->find();


        if (!empty($models)) {
            $models->modified_at = date('Y-m-d', $models->modified_at);
            $models->m_tambak_id = [
                'id' => $models->id_tambak,
                'kode'                       => $models->kode_tambak,
                'nama'                       => $models->nama_tambak,
                'fullkode'                   => $models->kode_tambak . ' - ' . $models->nama_tambak,
                'nama_petani'                => $models->nama_petani
            ];
            $models->supplier_id = ['fullSupplier'  => $models->kode_sup . ' - ' . $models->nama_supplier];
            $models->status           = (int) $models->status;
            $models->nama_rm          = (int) $models->asal_rm;
            $models->m_spesies_id     = (string) $models->m_spesies_id;
            $models->jam              = date('H:i', $models->jam_mulai);
            $models->penerimaan_order = $models->kode_jadwal . ' ( ' . date('d/m/Y', strtotime($models->tanggal_mulai_jadwal)) . ' - ' . date('d/m/Y', strtotime($models->tanggal_selesai_jadwal)) . ')';
            $models->supplier_id      = ['id' => $models->m_supplier_id, 'fullSupplier' => $models->kode_sup . ' - ' . $models->nama_supplier];
        }


        $data_detail = $db->select('
                t_penerimaan_det.*,SUM(t_penerimaan_det.kuantiti) as kuantiti ,
                m_size.nama as nama_size,
                m_size_kategori.nama as size_kategori,
                m_grade.nama as grade
               ')
            ->from('t_penerimaan_det')
            ->innerJoin('m_size', 't_penerimaan_det.m_size_id = m_size.id')
            ->leftjoin('m_size_kategori', 'm_size.m_size_kategori_id = m_size_kategori.id')
            ->leftjoin('m_grade', 't_penerimaan_det.m_grade_id = m_grade.id')
            ->where('t_penerimaan_det.t_penerimaan_id', '=', isset($models->id) ? $models->id : 0)
            ->groupBy('t_penerimaan_det.m_size_id, t_penerimaan_det.m_grade_id')
            ->orderBy("CASE WHEN (m_size_kategori.nama = 'LL' )THEN 1 WHEN ( m_size_kategori.nama = 'L' )THEN 2 WHEN ( m_size_kategori.nama = 'M' )THEN 3 WHEN ( m_size_kategori.nama = 'S' )THEN 4 WHEN ( m_size_kategori.nama = 'SS' )THEN 5 WHEN ( m_size_kategori.nama = 'C' )THEN 6 ELSE ( m_size_kategori.nama = 'C' ) END");

        $detail = $data_detail->findAll();
    }
    // echo json_encode($detail);die();
    $listGrade  = [];
    $arrperSize = [];
    $total      = [];
    $jumlah     = [];


    if (!empty($detail)) {

        $params['tanggal'] = isset($params['tanggal']) == is_int($params['tanggal']) ? $params['tanggal'] : strtotime($params['tanggal']);
        $newformat = date('Y-m-d', $params['tanggal']);

        foreach ($detail as $key => $value) {
            $harga = generateHarga($value->m_grade_id, $value->m_size_id, $newformat);
            $value->harga = $harga;
            if (!isset($arrperSize[$value->grade][$value->size_kategori])) {
                $arrperSize[$value->grade][$value->size_kategori]['nama']      = $value->size_kategori;
                $arrperSize[$value->grade][$value->size_kategori]['total']     = 0;
                $arrperSize[$value->grade][$value->size_kategori]['total_qty'] = 0;
                $arrperSize[$value->grade][$value->size_kategori]['rows']      = 0;
            }
            $value->jumlah                                                = $value->kuantiti * $harga;
            $arrperSize[$value->grade][$value->size_kategori]['detail'][] = $value;
            $arrperSize[$value->grade][$value->size_kategori]['total'] += $value->jumlah;
            $arrperSize[$value->grade][$value->size_kategori]['total_qty'] += $value->kuantiti;
            $arrperSize[$value->grade][$value->size_kategori]['rows'] += 1;
            $arrperSize[$value->grade][$value->size_kategori]['is_total_grade'] = 0;
        }
    }

    $arrBaru = [];
    $ind     = 0;
    foreach ($arrperSize as $key => $val) {
        $total = $qty = 0;
        foreach ($val as $keys => $vals) {
            $arrBaru[$ind] = (array) $vals;
            $total += $vals['total'];
            $qty += $vals['total_qty'];
            $ind++;
        }

        $arrNew = [
            'kategori'       => $key,
            'total'          => $total,
            'total_qty'      => number_format($qty, 2, '.', ''),
            'is_grade_total' => 1,
            'detail'         => [[
                'jumlah'   => $total,
                'kuantiti' => $qty,
            ]],
        ];
        array_push($arrBaru, $arrNew);
        $ind++;
    }




    $total_bawah    = 0;
    $total_kuantiti = 0;
    foreach ($arrBaru as $key => $value) {
        if (isset($value['is_grade_total']) && $value['is_grade_total'] == 1) {
            $total_bawah += $value['total'];
            $total_kuantiti += $value['total_qty'];
        }
    };

    // Pajak
    $potongan = $db->select('m_setting.pajak_npwp,m_setting.pajak_ditanggung_atina,
                                                  m_setting.biaya_perahu,m_setting.pajak_non_npwp')
        ->from('m_setting');
    $pt = $potongan->find();
    // ej($pt);

    $pajak1 = $pt->pajak_npwp;
    $pajak2 = $pt->pajak_non_npwp;

    //  npwp petani

    if (isset($params['pajak_atina']) && ($params['pajak_atina'] != "undefined") && ($params['pajak_atina'] != "")) {
        $pj_atina = ($params['pajak_atina']);
    } else {
        $pj_atina = $pt->pajak_ditanggung_atina;
    }



    //  jumlah pajak yang ditanggung pihak atina
    if (isset($pj_atina) && $pj_atina != " ") {
        $pajak_ditanggung       = $pj_atina * $total_bawah / 100;
        $pajak_ditanggung_atina = ['total' => $pj_atina * $total_bawah / 100, 'pajak' => $pj_atina];
    } else {
        $pajak_ditanggung       = $pj_atina * $total_bawah / 100;
        $pajak_ditanggung_atina = ['total' => $pj_atina * $total_bawah / 100, 'pajak' => $pj_atina];
    }

    // Biaya Perahu
    if (isset($models->pengiriman_id) && $models->pengiriman_id == 2) {
        if (isset($params['biaya_perahu']) && ($params['biaya_perahu'] != "undefined") && ($params['biaya_perahu'] != "")) {
            $biaya_kirim = ($params['biaya_perahu']);
        } else {
            $biaya_kirim = $pt->biaya_perahu;
        }
    } else {
        $biaya_kirim  = 0;
        $Biaya_perahu = 0;
    }


    $Biaya_perahu = $total_kuantiti * (int) $biaya_kirim;
    $total_rm     = $total_bawah - $Biaya_perahu;

    if ($params['m_tambak_id'] == "" ||  $params['m_tambak_id'] == "null") {
        $npwp =  $models->npwp_supplier;
    } else {
        $npwp = $models->npwp_petani;
    }

    if (isset($npwp) && !empty($npwp) && $npwp != " ") {
        $potongan_pajak = $total_bawah * $pajak2 / 100;
        if (isset($params['potongan_pph']) && ($params['potongan_pph'] == "minus")) {
            $potongan_rm_pph22 = $total_rm - $potongan_pajak;
        } else {
            $potongan_rm_pph22 = $total_rm + $potongan_pajak;
        }
        $total_potong_pph22 = ['total' => $potongan_rm_pph22, 'potongan' => $potongan_pajak, 'pajak' => $pajak2];
    } else {
        $potongan_pajak = $total_bawah * $pajak1 / 100;
        if (isset($params['potongan_pph']) && ($params['potongan_pph'] == "minus")) {
            $potongan_rm_pph22 = $total_rm - $potongan_pajak;
        } else {
            $potongan_rm_pph22 = $total_rm + $potongan_pajak;
        }
        $total_potong_pph22 = ['total' => $potongan_rm_pph22, 'potongan' => $potongan_pajak, 'pajak' => $pajak1];
    }
    // ej($total_potong_pph22);
    $grand_stl_pjk = $potongan_rm_pph22 - $pajak_ditanggung;

    // DATA FOR BOTTOM PRINT
    $pembelian = $db->select('*')
        ->from('m_setting_form')
        ->where('m_setting_form.reff_type', '=', 'nota pembelian')
        ->andWhere('m_setting_form.is_deleted', '=', 0);
    $kode_pembelian = $pembelian->find();

    // Data header

    $date = Date('d F Y');



    if (isset($params['is_export']) && 1 == $params['is_export']) {
        $view    = twigView();
        $content = $view->fetch('laporan/laporan-pembelian-insentif_export.html', [
            'parameter'         => $params['kelompok'],
            'list'              => $models,
            'data'              => $arrBaru,
            'total_rm'          => $total_rm,
            'total_kuantiti'    => $total_kuantiti,
            'total_bawah'       => $total_bawah,
            'potongan_pajak'    => $potongan_pajak,
            'pajak_npwp'        => $total_potong_pph22,
            'biaya_perahu'      => $Biaya_perahu,
            'pajak_atina'       => $pajak_ditanggung_atina,
            'param_perahu'      => $biaya_kirim,
            'besaran_pajak'     => $pj_atina,
            'potong_stlh_pajak' => $grand_stl_pjk,
            'status_potongan'   => $pj_atina == 0.25 ? 'tanpa_potongan' : 'potongan',

        ]);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;Filename="List pembelian Insentif.xls"');
        echo $content;
    } elseif (isset($params['is_print']) && 1 == $params['is_print']) {

        $view    = twigView();
        $content = $view->fetch('laporan/laporan-pembelian-insentif.html', [
            'gbr'               => $modelGbr,
            'parameter'         => $params['kelompok'],
            'list'              => $models,
            'kode'              => $kode_pembelian,
            'data'              => $arrBaru,
            'total_rm'          => $total_rm,
            'total_kuantiti'    => $total_kuantiti,
            'total_bawah'       => $total_bawah,
            'potongan_pajak'    => $potongan_pajak,
            'pajak_npwp'        => $total_potong_pph22,
            'biaya_perahu'      => $Biaya_perahu,
            'pajak_atina'       => $pajak_ditanggung_atina,
            'param_perahu'      => $biaya_kirim,
            'besaran_pajak'     => $pj_atina,
            'potong_stlh_pajak' => $grand_stl_pjk,
            'status_potongan'   => $pj_atina == 0.25 ? 'tanpa_potongan' : 'potongan',
            'setting_pajak'     => $params['potongan_pph'] != 'undefined' ? $params['potongan_pph'] : 'plus',

        ]);
        echo $content;
        echo '<script type="text/javascript">window.print();setTimeout(function () { window.close(); }, 500);</script>';

        //    // Extend the TCPDF class to create custom Header and Footer
        //        class MYPDF extends TCPDF {
        //     // Page footer
        //     public function Footer() {
        //         // Position at 15 mm from bottom
        //         $this->SetY(-15);
        //         // Set font
        //         $this->SetFont('helvetica', 'B', 9);
        //         $this->SetRightMargin(-5);
        //         // Page number
        //         $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        //         $this->Cell(10, 0, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');
        //     }
        //   }

        //     $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //     $pdf->SetCreator(PDF_CREATOR);
        //     $pdf->setPrintHeader(false);
        //     // $pdf->setPrintFooter(false);
        //     $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //     if (@file_exists(dirname(_FILE_) . '/lang/eng.php')) {
        //         require_once (dirname(_FILE_) . '/lang/eng.php');
        //         $pdf->setLanguageArray($l);
        //     }

        //     /** set margin */
        //     $pdf->SetMargins(2, 3, 1, true);

        //     /** set padding list */
        //     $pdf->setListIndentWidth(6);

        //     $tagvs = array(
        //         'h1' => array(
        //             0 => array('h' => 0, 'n' => 0),
        //             1 => array('h' => 1, 'n' => 2)),
        //         'h2' => array(
        //             0 => array('h' => 10, 'n' => 0),
        //             1 => array('h' => 1, 'n' => 2)),
        //         'label' => array(
        //             0 => array('h' => 1, 'n' => 1),
        //             1 => array('h' => 1, 'n' => 2)),
        //         'p' => array(
        //             0 => array('h' => '', 'n' => 0),
        //             1 => array('h' => '', 'n' => 0)),
        //         'div.atas' => array(
        //             0 => array('h' => 1, 'n' => 1),
        //             1 => array('h' => 1, 'n' => 1),
        //         ));
        //     $pdf->setHtmlVSpace($tagvs);

        //     /** Add landscape page size A4 */
        //     $pdf->AddPage('P', 'A4');

        //     /** Add content to pdf */
        //     $pdf->writeHTML($content, true, false, true, false, '');

        //     $response = $this->response->withHeader('Content-type', 'application/pdf');
        //     $response->write($pdf->Output('Nota Pembelian', 'S'));
        //     return $response;

        //    echo '<script type="text/javascript">window.print();setTimeout(function () { window.close(); }, 500);</script>';
    } else {
        return successResponse($response, [
            'list'              => $models,
            'data'              => $arrBaru,
            'total_rm'          => $total_rm,
            'total_kuantiti'    => $total_kuantiti,
            'total_bawah'       => $total_bawah,
            'potongan_pajak'    => $potongan_pajak,
            'pajak_npwp'        => $total_potong_pph22,
            'biaya_perahu'      => $Biaya_perahu,
            'pajak_atina'       => $pajak_ditanggung_atina,
            'param_perahu'      => $biaya_kirim,
            'besaran_pajak'     => $pj_atina,
            'potong_stlh_pajak' => $grand_stl_pjk,
            'status_potongan'   => $pj_atina == 0.25 ? 'tanpa_potongan' : 'potongan',
            'setting_pajak'     => $params['potongan_pph'] != 'undefined' ? $params['potongan_pph'] : 'plus',

        ]);
    }