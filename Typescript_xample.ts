 
 getData() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pagingType: "full_numbers",
      ajax: (dataTablesParameters: any, callback) => {
        let params = {
          params: JSON.stringify(this.modelParam),
          offset: dataTablesParameters.start,
          limit: dataTablesParameters.length,
        };
        this.landaService
          .DataGet("/appvoucher/index", params)
          .subscribe((res: any) => {
            this.listData = res.data.list;

            callback({
              recordsTotal: res.data.totalItems,
              recordsFiltered: res.data.totalItems,
              data: [],
            });
          });
      },
    };
  }


   checkDetailVoucher(data){
    var arrIdKetentuan = [];
    var is_data_double : boolean;
    this.listVoucherDet.forEach((val: any, key: any) => {
      if(arrIdKetentuan.includes(val.tipe_ketentuan)){
        is_data_double = true;
      }else{
        arrIdKetentuan.push(val.tipe_ketentuan);
      }
      
    });

    if(is_data_double){
      setTimeout(function() {
        data.tipe_ketentuan = null;
        data.nilai_ketentuan = '';
      }, 200);
      this.landaService.alertError('Mohon Maaf', 'Tipe Ketentuan sudah terpakai');
    }
    
  }


   restore(val) {
    const final = Object.assign(val);
    Swal.fire({
      title: this.translate.instant('Apakah anda yakin ?'),
      text: this.translate.instant('Merestore data Pengguna Referral akan berpengaruh terhadap data lainnya'),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#34c38f',
      cancelButtonColor: '#f46a6a',
      confirmButtonText: this.translate.instant('Ya, Restore data ini !')
    }).then(result => {
      if (result.value) {
        this.landaService.DataPost('/apppenggunareff/restore', final).subscribe((res: any) => {
          if (res.status_code == 200) {
            this.landaService.alertSuccess(this.translate.instant('Berhasil'), this.translate.instant('Data Pengguna Referral telah direstore !'));
            this.reloadDataTable();
          } else {
            this.landaService.alertError('Mohon Maaf', res.errors);
          }
        });
      }
    });
  }