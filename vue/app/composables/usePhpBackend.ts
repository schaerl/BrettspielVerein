import type { FetchResponse } from "ofetch";

export type BvzQuery = Record<string, string | boolean | number>;
export interface API<TYPE> {
  get(query?: BvzQuery): Promise<TYPE[]>;
  getRaw(query?: BvzQuery): Promise<FetchResponse<TYPE[]>>;
  post(body: TYPE): Promise<void>;
}

export default function<TYPE> (url: string): API<TYPE> {
  const { $api } = useNuxtApp();

  function trimStrings(body: TYPE) {
    let t: keyof TYPE;
    for (t in body) {
      if (typeof body[t] === "string") {
        // Because we check the type to be string, the assignment is fine
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        body[t] = (body[t] as string).trim() as any;
      }
    }
  };

  return {
    get: (query?: BvzQuery) => {
      if (query) {
        return $api(url, { query });
      }
      else {
        return $api(url) as Promise<TYPE[]>;
      }
    },

    getRaw: (query?: BvzQuery) => {
      if (query) {
        return $api.raw(url, { query }) as Promise<FetchResponse<TYPE[]>>;
      }
      else {
        return $api.raw(url) as Promise<FetchResponse<TYPE[]>>;
      }
    },

    post: (body: TYPE) => {
      trimStrings(body);
      return $api(url, { body: body as object, method: "POST" });
    },
  };
}
