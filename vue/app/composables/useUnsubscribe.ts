import { FetchError } from "ofetch";

export default async function (email: string, token: string) {
  const { del } = usePhpBackend("newsletter");
  try {
    await del({ email, token });
    return { message: "Erfolgreich vom Newsletter abgemeldet!", status: "success" as AlertType };
  }
  catch (err) {
    if (err instanceof FetchError && err.statusCode === 410) {
      return { message: "Email wurde bereits abgemeldet", status: "success" as AlertType };
    }
    else if (err instanceof FetchError && err.statusCode === 403) {
      return { message: "Das Token war falsch! Fehler beim Kopieren in den Browser?", status: "warning" as AlertType };
    }
    else {
      return { message: "Unbekannter Fehler. Bitte versuche es später nochmal.", status: "error" as AlertType };
    }
  }
}
