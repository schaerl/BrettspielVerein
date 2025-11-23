<script setup lang="ts">
const formData = ref<QuestionType>({
  "full-name": "",
  email: "",
  message: "",
});

const emit = defineEmits<{ onSubmission: [status: AlertStatus] }>();

const isFormValid = ref(false);
const { post } = usePhpBackend("question.php");

function submit(event: SubmitEvent) {
  if (isFormValid.value) {
    post(formData.value)
      .then(() => {
        emit("onSubmission", {
          message: "Deine Frage wurde geschickt. Wir melden uns bei dir!",
          type: "success",
        });
      })
      .catch((err) => {
        console.error(err);
        emit("onSubmission", {
          message:
            "Frage konnte nicht geschickt werden. Bitte versuche es später erneut",
          type: "warning",
        });
      });
  }
}
</script>

<template>
  <UForm v-model="isFormValid" @submit.prevent="submit">
    <UContainer>
     <!-- <VRow class="ga-4">
        <VTextField
          bg-color="background"
          label="Name"
          v-model="formData['full-name']"
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
      </VRow>
      <VRow>
        <VTextarea
          bg-color="background"
          label="Nachricht"
          v-model="formData.message"
          rounded="xl"
          :rules="[required]"
          variant="solo"
        >
        </VTextarea>
      </VRow>
      <VRow>
        <VBtn
          type="submit"
          class="w-100 text-onPrimary"
          color="primary"
          rounded="pill"
          >Absenden</VBtn
        >
      </VRow> -->
    </UContainer>
  </UForm>
</template>

<style scoped></style>
