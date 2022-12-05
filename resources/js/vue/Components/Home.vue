<template>
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-7 col-md-9">

            <div class="card">
                <div class="card-body">
                    <div class="clearfix mb-2">
                        <h5 class="float-start">
                            <i class="mdi mdi-reply"></i> اقدام برای پرداخت
                        </h5>

                        <div class="float-end">
                            <div v-show="loading" class="spinner-border" role="status">
                                <span class="visually-hidden">لطفا صبر کنید ...</span>
                            </div>
                        </div>
                    </div>

                    <p class="mb-2">
                        خدمت ارائه شده :
                        <span class="badge bg-secondary">خدمت تستی</span> 
                    </p>

                    <p>
                        مبلغ قابل پرداخت : 
                        <span class="badge bg-success">5000 ریال</span>
                    </p>

                    <form @submit.prevent="formSubmit">
                        <div class="mb-3">
                            <label for="cardNumber" class="form-label">شماره کارت</label>
                            <input :disabled="loading" v-model="cardNumber" @change="checkValidation" :class="{'form-control': true,'is-invalid': invalidCardNumber}" autofocus type="text" id="cardNumber" maxlength="16" placeholder="شماره کارت خود را وارد کنید ...">
                        </div>

                        <button :disabled="loading" class="btn btn-success w-100" type="submit">
                            <span v-show="!loading">تایید شماره کارت و پرداخت مبلغ</span>
                            <i v-show="loading" class="mdi mdi-cog me-2 mdi-spin"></i>
                            <span v-show="loading">لطفا صبر کنید ...</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            cardNumber: '',
            invalidCardNumber: false,
            loading: false,
        };
    },
    
    methods: {
        checkValidation() {
            this.cardNumber = this.cardNumber.trim();
            this.invalidCardNumber = !this.cardNumber;
        },

        formSubmit() {
            this.cardNumber = this.cardNumber.trim();
            if (!this.cardNumber) {
                this.invalidCardNumber = true;
                return;
            }
            else { this.invalidCardNumber = false; }

            this.loading = true;
            const searchParams = new URLSearchParams({'card_number': this.cardNumber});
            axios.post('/new-order',searchParams.toString(),{headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
            .then(response => {
                this.loading = false;
                let data = response.data;
                if (data.ok) {
                    window.location.href = data.data.redirect_url;
                }
                else { swalWarning(data.msg); }
            }).catch(err => {
                this.loading = false;
                swalConnectionError();
            });
        },
    },
};
</script>