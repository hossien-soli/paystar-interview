<template>
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-7 col-md-9">

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="mdi mdi-reply"></i> اقدام برای پرداخت
                    </h5>

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
                            <input v-model="cardNumber" @change="checkValidation" :class="{'form-control': true,'is-invalid': invalidCardNumber}" autofocus type="text" id="cardNumber" maxlength="16" placeholder="شماره کارت خود را وارد کنید ...">
                        </div>

                        <button class="btn btn-success w-100" type="submit">
                            تایید شماره کارت و پرداخت مبلغ
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

            
        },
    },
};
</script>