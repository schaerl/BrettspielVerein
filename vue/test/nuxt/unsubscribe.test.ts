import { describe, it, expect, vi } from "vitest";
import { mockNuxtImport, mountSuspended } from "@nuxt/test-utils/runtime";
import Unsubscribe from "../../app/pages/unsubscribe.vue";
import { UAlert } from "#components";

const { useUnsubscribeMock } = vi.hoisted(() => {
  return {
    useUnsubscribeMock: vi.fn((_) => {
      return { message: "Erfolgreich vom Newsletter abgemeldet!", status: "success" as AlertType };
    }),
  };
});

mockNuxtImport("useUnsubscribe", () => {
  return useUnsubscribeMock;
});

describe("unsubscribe", () => {
  it("require email", async () => {
    const component = await mountSuspended(Unsubscribe, {
      route: {
        query: {
          token: "a-token",
        },
      },
      scoped: true,
    });

    expect(component.findComponent(UAlert).props().color).toBe("warning");
    expect(component.findComponent(UAlert).props().title).toBe("Email muss angegeben werden!");
  });

  it("require token", async () => {
    const component = await mountSuspended(Unsubscribe, {
      route: {
        query: {
          email: "unit@test.com",
        },
      },
      scoped: true,
    });

    expect(component.findComponent(UAlert).props().color).toBe("warning");
    expect(component.findComponent(UAlert).props().title).toBe("Token muss angegeben werden!");
  });

  it("reports from useUnsubscribe when data valid", async () => {
    const component = await mountSuspended(Unsubscribe, {
      route: {
        query: {
          email: "unit@test.com",
          token: "a-token",
        },
      },
      scoped: true,
    });

    expect(component.findComponent(UAlert).props().color).toBe("success");
    expect(component.findComponent(UAlert).props().title).toBe("Erfolgreich vom Newsletter abgemeldet!");
  });
});
