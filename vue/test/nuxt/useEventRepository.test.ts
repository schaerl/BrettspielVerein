import { describe, it, expect, vi, type Mock } from "vitest";
import { mockDeep, type DeepMockProxy } from "vitest-mock-extended";
import { mockNuxtImport } from "@nuxt/test-utils/runtime";
import type { FetchResponse } from "ofetch";

const testEvent: EventData = {
  id: "1",
  date: "2025-12-12",
  location: "Spittelhof",
  name: "Brettspielabend",
  price: "5",
  start_time: "19:30:00",
};

const mockRawResponse: DeepMockProxy<FetchResponse<EventData[]>> = mockDeep<FetchResponse<EventData[]>>();
const { usePhpBackendMock }: { usePhpBackendMock: Mock<(_: string) => API<EventData>> } = vi.hoisted(() => {
  return {
    usePhpBackendMock: vi.fn((_) => {
      return {
        get: (_?: BvzQuery) => Promise.resolve([testEvent]),
        getRaw: (_?: BvzQuery) => Promise.resolve(mockRawResponse),
        post: (_: EventData) => Promise.resolve(),
      };
    }),
  };
});

mockNuxtImport("usePhpBackend", () => {
  return usePhpBackendMock;
});

describe("useEventRepository", () => {
  it("calls usePhpBackend for 'events'", () => {
    useEventRepository();

    expect(usePhpBackendMock).toBeCalledWith("/events");
  });

  it("returns data from php backend with adjusted date and time", async () => {
    const { repository } = useEventRepository();
    const data = await repository.getEventData();

    expect(data.length).toBe(1);
    expect(data[0]!.start_time).toBe("19:30");
    expect(data[0]!.date).toBe("12. Dez. 2025");
  });

  it("returns paginated data from php backend with total and adjusted date and time", async () => {
    mockRawResponse.headers.get.calledWith("X-Total-Count").mockReturnValue("1");
    mockRawResponse._data = [testEvent];

    const { repository } = useEventRepository();
    const response = await repository.getPagedEventData(1, 1);

    expect(response.total).toBe(1);
    expect(response.data.length).toBe(1);
    expect(response.data[0]!.start_time).toBe("19:30");
    expect(response.data[0]!.date).toBe("12. Dez. 2025");
  });
});
