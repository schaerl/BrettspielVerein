import { describe, it, expect } from "vitest";
import { mountSuspended } from "@nuxt/test-utils/runtime";
import { EventDetails, UBadge } from "#components";

describe("EventDetails", () => {
  it("displays all data and formats price", async () => {
    const wrapper = await mountSuspended(EventDetails, {
      props: {
        data: {
          id: "1",
          date: "2025-05-14",
          location: "Spittelhof",
          price: "5",
          start_time: "19:30",
          name: "Test Event",
          extra: "Extra info",
        },
      },
    });

    const datePill = wrapper.findComponent(UBadge);
    expect(datePill.text()).toBe("2025-05-14");

    const header = wrapper.find("h2");
    expect(header.text()).toBe("Test Event");

    const tableRows = wrapper.findAll("tr");
    expect(tableRows.length).toBe(5);

    expect(tableRows[0]!.text()).toBe("Ort:Spittelhof");
    expect(tableRows[1]!.text()).toBe("Zeit:19:30");
    expect(tableRows[2]!.text()).toBe("Eintritt:5.-");
    expect(tableRows[3]!.text()).toBe(""); // nbsp
    expect(tableRows[4]!.text()).toBe("Extra Informationen:Extra info");
  });

  it("displays a dash when price is not a number", async () => {
    const wrapper = await mountSuspended(EventDetails, {
      props: {
        data: {
          id: "1",
          date: "2025-05-14", location: "Spittelhof",
          price: "Not a number",
          start_time: "19:30",
          name: "Test Event",
        },
      },
    });

    const tableRows = wrapper.findAll("tr");
    expect(tableRows[2]!.text()).toBe("Eintritt:-");
  });

  it("makes 'extra' a link if it starts with 'http'", async () => {
    const wrapper = await mountSuspended(EventDetails, {
      props: {
        data: {
          id: "1",
          date: "2025-05-14",
          location: "Spittelhof",
          price: "5",
          start_time: "19:30",
          name: "Test Event",
          extra: "http://example.com",
        },
      },
    });

    const anchor = wrapper.find("a");
    expect(anchor.text()).toBe("http://example.com");
  });
});
