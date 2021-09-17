<template>
  <el-container>
    <el-aside width="200px">
      <el-menu class="main-menu" default-active="2" @open="handleOpen" @close="handleClose">
        <el-menu-item index="1">
          <i class="el-icon-menu"></i>
          <span>Dashboard</span>
        </el-menu-item>
        <el-menu-item index="2">
          <i class="el-icon-document"></i>
          <span>Accounts</span>
        </el-menu-item>
        <el-menu-item index="3">
          <i class="el-icon-setting"></i>
          <span>Parameters</span>
        </el-menu-item>
      </el-menu>
    </el-aside>
    <el-container>
      <el-header>Header</el-header>
      <el-main>
        <el-row :gutter="20">
          <el-col :span="8" v-for="account in accounts">
            <Account :account="account"></Account>
          </el-col>
        </el-row>
      </el-main>
      <el-footer>Footer</el-footer>
    </el-container>
  </el-container>
</template>

<script>
import {useStore} from "vuex";
import axios from "axios";

import Account from './../components/Account'
import {computed, ref} from "vue";

export default {
  components: {
    Account
  },
  setup(props) {
    const store = useStore()

    axios.get('/accounts')
        .then(response => {
          response.data.forEach(account => store.commit('addAccount', account))
        })

    return {
      accounts: computed(() => store.getters.getAccounts)
    }
  }
}
</script>

<style>
  .main-menu {
    height: 100%;
  }
</style>
