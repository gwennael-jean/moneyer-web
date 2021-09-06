<style>

</style>

<template>
  <div>
    <h1>Resources</h1>
  </div>

  <div>
    <h1>Charges</h1>
    <div class="row">
      <div class="col-12 col-sm-6 col-lg-4" v-for="account in accounts">
        <Account></Account>
      </div>
    </div>
  </div>

  <div>
    <h1>Transfers</h1>
  </div>

  <div>
    <h1>Living Wage</h1>
  </div>
</template>

<script>

import Account from './../components/Account'

export default {
  components: {
    Account
  },
  data: () => {
    return {
      accounts: []
    }
  },
}
</script>
