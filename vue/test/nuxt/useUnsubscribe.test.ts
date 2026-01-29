import { describe, it, expect, vi, afterAll } from "vitest";
import { mockNuxtImport } from "@nuxt/test-utils/runtime";
import { FetchError } from "ofetch";
import { mockDeep, mockReset, type DeepMockProxy } from "vitest-mock-extended";

const mockErrorResponse: DeepMockProxy<FetchError> = mockDeep<FetchError>();
Object.setPrototypeOf(mockErrorResponse, FetchError.prototype);
const { usePhpBackendMock } = vi.hoisted(() => {
  return {
    usePhpBackendMock: vi.fn((_) => {
      return {
        del: (_: object) => Promise.resolve(),
      };
    }),
  };
});

mockNuxtImport("usePhpBackend", () => {
  return usePhpBackendMock;
});

describe("useUnsubscribe", () => {
  afterAll(() => {
    mockReset(mockErrorResponse);
  });

  it("when backend reports ok, returns success message", async () => {
    const result = await useUnsubscribe("email", "token");

    expect(result.message).toBe("Erfolgreich vom Newsletter abgemeldet!");
    expect(result.status).toBe("success");
  });

  it("when backend reports 410, returns success message", async () => {
    mockErrorResponse.statusCode = 410;
    usePhpBackendMock.mockImplementation(() => {
      return {
        del: (_: object) => Promise.reject(mockErrorResponse),
      };
    });
    const result = await useUnsubscribe("email", "token");

    expect(result.message).toBe("Email wurde bereits abgemeldet");
    expect(result.status).toBe("success");
  });

  it("when backend reports 403, returns warning message", async () => {
    mockErrorResponse.statusCode = 403;
    usePhpBackendMock.mockImplementation(() => {
      return {
        del: (_: object) => Promise.reject(mockErrorResponse),
      };
    });
    const result = await useUnsubscribe("email", "token");

    expect(result.message).toBe("Das Token war falsch! Fehler beim Kopieren in den Browser?");
    expect(result.status).toBe("warning");
  });

  it("when backend reports unknown error, returns error message", async () => {
    mockErrorResponse.statusCode = 500;
    usePhpBackendMock.mockImplementation(() => {
      return {
        del: (_: object) => Promise.reject(mockErrorResponse),
      };
    });
    const result = await useUnsubscribe("email", "token");

    expect(result.message).toBe("Unbekannter Fehler. Bitte versuche es später nochmal.");
    expect(result.status).toBe("error");
  });
});
