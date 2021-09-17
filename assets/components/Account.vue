<template>
  <el-card>
    <template #header>
      <div class="card-header">
        <span>{{ account.name }}</span>
      </div>
    </template>

    <el-table :data="account.charges" style="width: 100%">
      <el-table-column prop="name" label="Name"></el-table-column>
      <el-table-column prop="amount" label="Amount" width=80 align="right"></el-table-column>
    </el-table>
  </el-card>
</template>

<script>

export default {
  props: {
    account: Object,
  },
}
</script>

<style>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.text {
  font-size: 14px;
}

.item {
  margin-bottom: 18px;
}

.box-card {
  width: 480px;
}
</style>
