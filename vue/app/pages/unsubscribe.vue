<script setup lang="ts">
const params = useRoute().query;

const email = params["email"] as string;
const token = params["token"] as string;

async function calculateStatus() {
  if (!email) {
    return { message: "Email muss angegeben werden!", status: "warning" as AlertType };
  }
  else if (!token) {
    return { message: "Token muss angegeben werden!", status: "warning" as AlertType };
  }
  else {
    return await useUnsubscribe(email, token);
  }
}
const { data, error } = await useLazyAsyncData<{ message: string; status: AlertType }>("unsubscription",
  () => calculateStatus(), { server: false });
</script>

<template>
  <UContainer>
    <UAlert
      v-if="data || error"
      :title="data?.message ?? error?.message"
      :color="error ? 'error' : data?.status"
      class="my-4"
    />
  </UContainer>
</template>

<style scoped>
</style>
