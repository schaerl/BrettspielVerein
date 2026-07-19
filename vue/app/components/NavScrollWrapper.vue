<script setup lang="ts">
const { title } = defineProps<{
  title: string;
}>();

const el = useTemplateRef("el");

const { top } = useElementBounding(el);
const { arrivedState } = useWindowScroll();
const scrolledOver = computed(() => top.value < 65 || arrivedState.bottom);

const emit = defineEmits<{
  provideNavigation: [navInfo: HeaderSpec];
}>();

onMounted(() => {
  emit("provideNavigation", { displayName: title, scrolledBeginningToTop: scrolledOver, goTo: () => {
    el.value!.scrollIntoView({ behavior: "smooth" });
  } });
});

function generateId(input: string) {
  return input.replaceAll(" ", "_").toLocaleLowerCase();
}
</script>

<template>
  <div
    :id="generateId(title)"
    ref="el"
    class="scroll-m-(--ui-header-height)"
  >
    <slot />
  </div>
</template>
