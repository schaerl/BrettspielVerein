import type { FetchResponse } from "ofetch";

function shortenDateAndTime(data: EventData) {
  data.start_time = data.start_time.substring(0, 5);
  const date = new Date(data.date);
  data.date = date.toLocaleString("de", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

export default function () {
  const { get, getRaw } = usePhpBackend("/events");

  return {
    repository: {
      async getEventData(query?: Record<string, string | boolean | number>): Promise<EventData[]> {
        const promise: Promise<EventData[]> = get(query) as Promise<EventData[]>;
        return promise.then((data) => {
          data.forEach(item => shortenDateAndTime(item));
          return data;
        });
      },

      async getPagedEventData(page: number, pageSize: number): Promise<PagedData<EventData>> {
        const query = { page, pageSize };
        const response = await getRaw(query) as FetchResponse<EventData[]>;
        const total = Number(response.headers.get("X-Total-Count"));
        const data = response._data!;

        return {
          total,
          data,
        };
      },
    },
  };
}
