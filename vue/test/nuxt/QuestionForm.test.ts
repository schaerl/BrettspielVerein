import { describe, it, expect, vi, afterAll } from "vitest";
import { flushPromises } from "@vue/test-utils";
import { mountSuspended, mockNuxtImport } from "@nuxt/test-utils/runtime";
import { QuestionForm } from "#components";

const { usePhpBackendMock } = vi.hoisted(() => {
  return {
    usePhpBackendMock: vi.fn((_) => {
      return {
        get: () => [],
        post: (_: object) => Promise.resolve(), // simulate successful submission
      };
    }),
  };
});

mockNuxtImport("usePhpBackend", () => {
  return usePhpBackendMock;
});

describe("QuestionForm", () => {
  const consoleMock = vi.spyOn(console, "error").mockImplementation(() => undefined);
  afterAll(() => {
    consoleMock.mockReset();
  });

  it("uses phpBackend with 'question'", async () => {
    await mountSuspended(QuestionForm);

    expect(usePhpBackendMock).toHaveBeenLastCalledWith("question");
  });

  it("does not submit when data is not valid", async () => {
    const wrapper = await mountSuspended(QuestionForm);

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");
    await flushPromises();

    expect(wrapper.emitted()).toStrictEqual({});
    const error = wrapper.findAll(`[data-slot="error"]`);
    expect(error).toHaveLength(3);
  });

  describe("error messages", () => {
    it("is correct for missing name", async () => {
      const wrapper = await mountSuspended(QuestionForm);
      const form = wrapper.find("form");

      const emailInput = wrapper.find("[label='email']");
      const messageInput = wrapper.find("[label='message']");

      await emailInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Namen angeben");
    });

    it("is correct for missing email", async () => {
      const wrapper = await mountSuspended(QuestionForm);
      const form = wrapper.find("form");

      const fullNameInput = wrapper.find("[label='fullName']");
      const messageInput = wrapper.find("[label='message']");

      await fullNameInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Ungültige E-Mail");
    });

    it("is correct for missing message", async () => {
      const wrapper = await mountSuspended(QuestionForm);
      const form = wrapper.find("form");

      const fullNameInput = wrapper.find("[label='fullName']");
      const emailInput = wrapper.find("[label='email']");

      await fullNameInput.setValue("Unit Test");
      await emailInput.setValue("test@unit-test.com");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Nachricht angeben");
    });
  });

  it("submits successfully when data entered", async () => {
    const wrapper = await mountSuspended(QuestionForm);
    const form = wrapper.find("form");
    const fullNameInput = wrapper.find("[label='fullName']");
    const emailInput = wrapper.find("[label='email']");
    const messageInput = wrapper.find("[label='message']");

    await fullNameInput.setValue("Unit Test");
    await emailInput.setValue("test@unit-test.com");
    await messageInput.setValue("This is a test message");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Deine Frage wurde geschickt. Wir melden uns bei dir!",
      type: "success",
    });
  });

  it("emits a warning when submit resolves to error", async () => {
    usePhpBackendMock.mockImplementation(() => {
      return {
        get: () => [],
        post: (_: object) => Promise.reject("Didn't work"),
      };
    });

    const wrapper = await mountSuspended(QuestionForm);
    const form = wrapper.find("form");
    const fullNameInput = wrapper.find("[label='fullName']");
    const emailInput = wrapper.find("[label='email']");
    const messageInput = wrapper.find("[label='message']");

    await fullNameInput.setValue("Unit Test");
    await emailInput.setValue("test@unit-test.com");
    await messageInput.setValue("This is a test message");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Frage konnte nicht geschickt werden. Bitte versuche es später erneut",
      type: "warning",
    });

    expect(consoleMock).toHaveBeenCalledWith("Didn't work");
  });
});
