import { describe, it, expect, vi, afterAll } from "vitest";
import { flushPromises } from "@vue/test-utils";
import { mountSuspended, mockNuxtImport } from "@nuxt/test-utils/runtime";
import { NewsletterForm } from "#components";
import type { FetchError } from "ofetch";
import { mockDeep, mockReset, type DeepMockProxy } from "vitest-mock-extended";

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

describe("NewsletterForm", () => {
  it("uses phpBackend with 'newsletter'", async () => {
    await mountSuspended(NewsletterForm);

    expect(usePhpBackendMock).toHaveBeenLastCalledWith("newsletter");
  });

  it("does not submit when data is not valid", async () => {
    const wrapper = await mountSuspended(NewsletterForm);

    await wrapper.trigger("submit.prevent");

    expect(wrapper.emitted()).toStrictEqual({});
  });

  it("validates form on submission", async () => {
    const wrapper = await mountSuspended(NewsletterForm);
    const form = wrapper.find("form");

    await form.trigger("submit.prevent");
    await flushPromises();

    const error = wrapper.find(`[data-slot="error"]`);
    expect(error.text()).toBe("Ungültige E-Mail");
  });

  it("submits successfully when email entered", async () => {
    const wrapper = await mountSuspended(NewsletterForm);
    const form = wrapper.find("form");
    const input = wrapper.find("input");

    await input.setValue("test@unit-test.com");
    await form.trigger("submit.prevent");
    await flushPromises();

    const emitted = wrapper.emitted("onSubmission");
    expect(emitted).toHaveLength(1);
    expect(emitted![0]![0]).toStrictEqual({
      message: "Erfolgreich für den Newsletter registriert",
      type: "success",
    });
  });

  describe("when is not successful", () => {
    const mockResponse: DeepMockProxy<FetchError> = mockDeep<FetchError>();
    afterAll(() => {
      mockReset(mockResponse);
    });

    it("emits warning with specific message when submit resolves to 409", async () => {
      mockResponse.statusCode = 409;
      mockResponse.message = "Didn't work";
      usePhpBackendMock.mockImplementation(() => {
        return {
          get: () => [],
          post: (_: object) => Promise.reject(mockResponse),
        };
      });

      const wrapper = await mountSuspended(NewsletterForm);
      const form = wrapper.find("form");
      const input = wrapper.find("input");

      await input.setValue("test@unit-test.com");
      await form.trigger("submit.prevent");
      await flushPromises();

      const emitted = wrapper.emitted("onSubmission");
      expect(emitted).toHaveLength(1);
      expect(emitted![0]![0]).toStrictEqual({
        message: "Diese Email ist bereits registriert.",
        type: "warning",
      });
    });

    it("emits warning with generic message otherwise", async () => {
      mockResponse.statusCode = 500;
      mockResponse.message = "Didn't work";
      usePhpBackendMock.mockImplementation(() => {
        return {
          get: () => [],
          post: (_: object) => Promise.reject(mockResponse),
        };
      });

      const wrapper = await mountSuspended(NewsletterForm);
      const form = wrapper.find("form");
      const input = wrapper.find("input");

      await input.setValue("test@unit-test.com");
      await form.trigger("submit.prevent");
      await flushPromises();

      const emitted = wrapper.emitted("onSubmission");
      expect(emitted).toHaveLength(1);
      expect(emitted![0]![0]).toStrictEqual({
        message: "Registrierung fehlgeschlagen. Bitte versuche es später erneut",
        type: "warning",
      });
    });
  });
});
