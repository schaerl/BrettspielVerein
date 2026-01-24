import { describe, it, expect, vi, afterAll } from "vitest";
import { flushPromises } from "@vue/test-utils";
import { mountSuspended, mockNuxtImport } from "@nuxt/test-utils/runtime";
import { SignUpForm } from "#components";

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

describe("SignUpForm", () => {
  const consoleMock = vi.spyOn(console, "error").mockImplementation(() => undefined);
  afterAll(() => {
    consoleMock.mockReset();
  });

  it("uses phpBackend with 'member'", async () => {
    await mountSuspended(SignUpForm);

    expect(usePhpBackendMock).toHaveBeenLastCalledWith("member");
  });

  it("does not submit when data is not valid", async () => {
    const wrapper = await mountSuspended(SignUpForm);

    const form = wrapper.find("form");
    await form.trigger("submit.prevent");
    await flushPromises();

    expect(wrapper.emitted()).toStrictEqual({});
    const error = wrapper.findAll(`[data-slot="error"]`);
    expect(error).toHaveLength(5);
  });

  describe("error messages", () => {
    it("is correct for missing first name", async () => {
      const wrapper = await mountSuspended(SignUpForm);

      const form = wrapper.find("form");

      const lastNameInput = wrapper.find("[label='lastName']");
      const addressInput = wrapper.find("[label='address1']");
      const address2Input = wrapper.find("[label='address2']");
      const emailInput = wrapper.find("[label='email']");
      const messageInput = wrapper.find("[label='message']");

      await lastNameInput.setValue("Test");
      await addressInput.setValue("Testroad 2");
      await address2Input.setValue("1000 Testcity");
      await emailInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Vornamen angeben");
    });

    it("is correct for missing last name", async () => {
      const wrapper = await mountSuspended(SignUpForm);

      const form = wrapper.find("form");

      const firstNameInput = wrapper.find("[label='firstName']");
      const addressInput = wrapper.find("[label='address1']");
      const address2Input = wrapper.find("[label='address2']");
      const emailInput = wrapper.find("[label='email']");
      const messageInput = wrapper.find("[label='message']");

      await firstNameInput.setValue("Unit");
      await addressInput.setValue("Testroad 2");
      await address2Input.setValue("1000 Testcity");
      await emailInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Nachnamen angeben");
    });

    it("is correct for missing address line 1", async () => {
      const wrapper = await mountSuspended(SignUpForm);

      const form = wrapper.find("form");

      const firstNameInput = wrapper.find("[label='firstName']");
      const lastNameInput = wrapper.find("[label='lastName']");
      const address2Input = wrapper.find("[label='address2']");
      const emailInput = wrapper.find("[label='email']");
      const messageInput = wrapper.find("[label='message']");

      await firstNameInput.setValue("Unit");
      await lastNameInput.setValue("Test");
      await address2Input.setValue("1000 Testcity");
      await emailInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Strasse angeben");
    });

    it("is correct for missing address line 2", async () => {
      const wrapper = await mountSuspended(SignUpForm);

      const form = wrapper.find("form");

      const firstNameInput = wrapper.find("[label='firstName']");
      const lastNameInput = wrapper.find("[label='lastName']");
      const addressInput = wrapper.find("[label='address1']");
      const emailInput = wrapper.find("[label='email']");
      const messageInput = wrapper.find("[label='message']");

      await firstNameInput.setValue("Unit");
      await lastNameInput.setValue("Test");
      await addressInput.setValue("Testroad 2");
      await emailInput.setValue("test@unit-test.com");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Bitte Ort angeben");
    });

    it("is correct for missing email", async () => {
      const wrapper = await mountSuspended(SignUpForm);

      const form = wrapper.find("form");

      const firstNameInput = wrapper.find("[label='firstName']");
      const lastNameInput = wrapper.find("[label='lastName']");
      const addressInput = wrapper.find("[label='address1']");
      const address2Input = wrapper.find("[label='address2']");
      const messageInput = wrapper.find("[label='message']");

      await firstNameInput.setValue("Unit");
      await lastNameInput.setValue("Test");
      await addressInput.setValue("Testroad 2");
      await address2Input.setValue("1000 Testcity");
      await messageInput.setValue("This is a test message");
      await form.trigger("submit.prevent");
      await flushPromises();

      const error = wrapper.find(`[data-slot="error"]`);
      expect(error.text()).toBe("Ungültige E-Mail");
    });
  });

  it("submits successfully when data entered", async () => {
    const wrapper = await mountSuspended(SignUpForm);

    const form = wrapper.find("form");

    const firstNameInput = wrapper.find("[label='firstName']");
    const lastNameInput = wrapper.find("[label='lastName']");
    const addressInput = wrapper.find("[label='address1']");
    const address2Input = wrapper.find("[label='address2']");
    const emailInput = wrapper.find("[label='email']");
    const messageInput = wrapper.find("[label='message']");

    await firstNameInput.setValue("Unit");
    await lastNameInput.setValue("Test");
    await addressInput.setValue("Testroad 2");
    await address2Input.setValue("1000 Testcity");
    await emailInput.setValue("test@unit-test.com");
    await messageInput.setValue("This is a test message");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Erfolgreich Anmeldung als Mitglied eingereicht",
      type: "success",
    });
  });

  it("submits successfully even when no message entered", async () => {
    const wrapper = await mountSuspended(SignUpForm);

    const form = wrapper.find("form");

    const firstNameInput = wrapper.find("[label='firstName']");
    const lastNameInput = wrapper.find("[label='lastName']");
    const addressInput = wrapper.find("[label='address1']");
    const address2Input = wrapper.find("[label='address2']");
    const emailInput = wrapper.find("[label='email']");

    await firstNameInput.setValue("Unit");
    await lastNameInput.setValue("Test");
    await addressInput.setValue("Testroad 2");
    await address2Input.setValue("1000 Testcity");
    await emailInput.setValue("test@unit-test.com");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Erfolgreich Anmeldung als Mitglied eingereicht",
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

    const wrapper = await mountSuspended(SignUpForm);

    const form = wrapper.find("form");

    const firstNameInput = wrapper.find("[label='firstName']");
    const lastNameInput = wrapper.find("[label='lastName']");
    const addressInput = wrapper.find("[label='address1']");
    const address2Input = wrapper.find("[label='address2']");
    const emailInput = wrapper.find("[label='email']");
    const messageInput = wrapper.find("[label='message']");

    await firstNameInput.setValue("Unit");
    await lastNameInput.setValue("Test");
    await addressInput.setValue("Testroad 2");
    await address2Input.setValue("1000 Testcity");
    await emailInput.setValue("test@unit-test.com");
    await messageInput.setValue("This is a test message");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Registrierung fehlgeschlagen. Bitte versuche es später erneut",
      type: "warning",
    });

    expect(consoleMock).toHaveBeenCalledWith("Didn't work");
  });
});
