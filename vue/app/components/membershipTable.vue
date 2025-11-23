<script setup lang="ts"></script>

<template>
  <div class="">
    <UTable class="w-100 rounded-xl">
      <thead>
        <tr>
          <th>Jährlich</th>
          <th>50.-</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1 Jahr Gratiseintritt</td>
          <td>
            <UIcon icon="mdi-check-circle" color="green" size="small"></UIcon>
          </td>
        </tr>
        <tr>
          <td>Verleih der Vereinsspiele</td>
          <td>
            <UIcon icon="mdi-check-circle" color="green" size="small"></UIcon>
          </td>
        </tr>
      </tbody>
    </UTable>
  </div>
</template>

<style scoped lang="scss">
table {
  --table-padding: 15px;
  border-collapse: collapse;
  thead {
    tr {
      background-color: rgb(var(--v-theme-header));
      color: white;
    }
  }
  tbody {
    tr:nth-child(even) {
      background-color: rgb(var(--v-theme-secondary));
    }
  }
  td,
  th {
    padding: var(--table-padding);
    font-weight: bold;
  }
  th {
    justify-self: center;
  }
}
</style>
