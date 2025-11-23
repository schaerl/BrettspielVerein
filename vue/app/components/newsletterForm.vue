<script setup lang="ts">
const isFormValid = ref(false);
const data = ref({ email: "" });

const { post } = usePhpBackend("newsletter.php");

const emit = defineEmits<{ onSubmission: [status: AlertStatus] }>();
function submit(event: SubmitEvent) {
  if (isFormValid.value) {
    post(data.value)
      .then(() => {
        emit("onSubmission", {
          message: "Erfolgreich für den Newsletter registriert",
          type: "success",
        });
      })
      .catch((err) => {
        console.error(err);
        emit("onSubmission", {
          message:
            "Registrierung fehlgeschlagen. Bitte versuche es später erneut",
          type: "warning",
        });
      });
  }
}
</script>

<template>
  <UForm @submit.prevent v-model="isFormValid">
    <UInput
      label="Email"
      elevation="0"
      rounded="xl"
      :rules="[required, validEmail]"
      type="email"
      variant="solo"
      v-model="data.email"
    >
      <template #append-inner>
        <UButton
          @click="submit"
          class="text-onPrimary"
          elevation="0"
          rounded="pill"
          type="submit"
          color="primary"
          >Abonnieren</UButton
        >
      </template>
    </UInput>
  </UForm>
</template>

<style lang="scss" scoped></style>
