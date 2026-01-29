import { describe, it, expect, vi, type Mock } from "vitest";
import { mockNuxtImport } from "@nuxt/test-utils/runtime";

type ApiMock = Mock<(_: string, __: object) => Promise<string>>;
type OFetchMock = ApiMock & { raw: ApiMock };

const { useNuxtAppMock, mockApi } = vi.hoisted(() => {
  const mockApi = vi.fn((_: string, __ = {}) => Promise.resolve("Direct")) as OFetchMock;
  const raw = vi.fn((_: string, __ = {}) => Promise.resolve("Raw"));
  mockApi.raw = raw;
  return {
    useNuxtAppMock: vi.fn((_) => {
      return {
        $api: mockApi,
      };
    }),
    mockApi,
  };
});

mockNuxtImport("useNuxtApp", () => {
  return useNuxtAppMock;
});

describe("usePhpBackend", () => {
  describe("get", () => {
    it("calls api directly with provided url", async () => {
      const { get } = usePhpBackend("dummy.php");
      const result = await get();

      expect(result).toBe("Direct");
      expect(mockApi).toBeCalledWith("dummy.php");
    });
  });

  describe("get", () => {
    it("calls api with get", async () => {
      const { get } = usePhpBackend("dummy.php");
      const result = await get();

      expect(result).toBe("Direct");
      expect(mockApi).toBeCalledWith("dummy.php");
    });

    it("calls api with get, and query params if passed", async () => {
      const { get } = usePhpBackend("dummy.php");
      const result = await get({ hello: "world", num: 1, bool: false });

      expect(result).toBe("Direct");
      expect(mockApi).toBeCalledWith("dummy.php", {
        query: {
          bool: false,
          hello: "world",
          num: 1,
        },
      });
    });
  });

  describe("getRaw", () => {
    it("calls api.raw", async () => {
      const { getRaw } = usePhpBackend("dummy.php");
      const result = await getRaw();

      expect(result).toBe("Raw");
      expect(mockApi.raw).toBeCalledWith("dummy.php");
    });

    it("calls api.raw with query params if passed", async () => {
      const { getRaw } = usePhpBackend("dummy.php");
      const result = await getRaw({ hello: "world", num: 1, bool: false });

      expect(result).toBe("Raw");
      expect(mockApi.raw).toBeCalledWith("dummy.php", {
        query: {
          bool: false,
          hello: "world",
          num: 1,
        },
      });
    });
  });

  describe("post", () => {
    it("calls api at provided url, with provided body, as a POST", async () => {
      const { post } = usePhpBackend("dummy.php");
      const body = { a: "Unit", b: 4 };
      const result = await post(body);

      expect(result).toBe("Direct");
      expect(mockApi).toBeCalledWith("dummy.php", {
        body,
        method: "POST",
      });
    });

    it("trims all strings", async () => {
      const { post } = usePhpBackend("dummy.php");
      const body = { a: "    Unit   test  ", b: 4, c: "Another test     ", d: "   " };
      const result = await post(body);

      expect(result).toBe("Direct");
      const expectedBody = { a: "Unit   test", b: 4, c: "Another test", d: "" };
      expect(mockApi).toBeCalledWith("dummy.php", {
        body: expectedBody,
        method: "POST",
      });
    });
  });

  describe("delete", () => {
    it("calls api at provided url, with provided query, as a DELETE", async () => {
      const { del } = usePhpBackend("dummy.php");
      const query = { a: "Unit", b: 4 };
      const result = await del(query);

      expect(result).toBe("Direct");
      expect(mockApi).toBeCalledWith("dummy.php", {
        query,
        method: "DELETE",
      });
    });
  });
});
