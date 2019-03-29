// 註冊一個名為 user-addresses-create-and-edit 的組件
Vue.component('user-addresses-create-and-edit', {
  // 組件的數據
  data() {
    return {
      province: '', // 省
      city: '', // 市
      district: '', // 區
    }
  },
  methods: {
    // 把參數 val 中的值保存到組件的數據中
    // onDistrictChanged方法用於處理select-district組件拋出的change事件，把事件的數據複製到本組件中
    onDistrictChanged(val) {
      if (val.length === 3) {
        this.province = val[0];
        this.city = val[1];
        this.district = val[2];
      }
    }
  }
});
