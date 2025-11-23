<script setup lang="ts">
const formData = ref<RegistrationData>({
  firstName: "",
  lastName: "",
  address: "",
  address2: "",
  email: "",
  message: "",
});
const isFormValid = ref(false);

const { post } = usePhpBackend("register.php");
const emit = defineEmits<{ onSubmission: [status: AlertStatus] }>();
function submit(event: SubmitEvent) {
  if (isFormValid.value) {
    post(formData.value)
      .then(() => {
        emit("onSubmission", {
          message: "Erfolgreich als Mitglied angemeldet",
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
	<!--  <VSheet class="pa-5 bg-header rounded-xl"> -->
    <h3 class="mb-3">Werde Mitglied</h3>
    <UForm v-model="isFormValid" @submit.prevent="submit">
    <!--  <VTextField
        bg-color="background"
        label="Vorname"
        v-model="formData.firstName"
        rounded="xl"
        :rules="[required]"
        validate-on="blur"
        variant="solo"
      ></VTextField>
      <VTextField
        bg-color="background"
        label="Nachname"
        v-model="formData.lastName"
        rounded="xl"
        :rules="[required]"
        validate-on="blur"
        variant="solo"
      ></VTextField>
      <VTextField
        bg-color="background"
        label="Adresse"
        v-model="formData.address"
        rounded="xl"
        :rules="[required]"
        validate-on="blur"
        variant="solo"
      ></VTextField>
      <VTextField
        bg-color="background"
        label="PLZ + Wohnort"
        v-model="formData.address2"
        rounded="xl"
        :rules="[required]"
        validate-on="blur"
        variant="solo"
      ></VTextField>
      <VTextField
        bg-color="background"
        type="email"
        label="Email"
        v-model="formData.email"
        rounded="xl"
        :rules="[required, validEmail]"
        validate-on="blur"
        variant="solo"
      ></VTextField>
      <VTextarea
        bg-color="background"
        label="Kommentar"
        v-model="formData.message"
        rounded="xl"
        variant="solo"
      ></VTextarea>
      <UButton
        type="submit"
        class="w-100 text-onPrimary"
        color="primary"
        rounded="pill"
        >Anmelden</UButton
	> -->
    </UForm>
    <!--/VSheet-->
</template>

<style lang="scss" scoped></style>
