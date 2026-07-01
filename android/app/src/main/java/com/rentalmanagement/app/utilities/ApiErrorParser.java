package com.rentalmanagement.app.utilities;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

import java.io.IOException;
import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

import okhttp3.ResponseBody;
import retrofit2.Response;

public final class ApiErrorParser {

    private ApiErrorParser() {
    }

    public static ParsedApiError parse(Response<?> response, String fallbackMessage) {
        if (response == null) {
            return new ParsedApiError(fallbackMessage, Collections.emptyMap());
        }

        String message = fallbackMessage;
        Map<String, String> fieldErrors = new HashMap<>();

        ResponseBody errorBody = response.errorBody();
        if (errorBody == null) {
            return new ParsedApiError(message, fieldErrors);
        }

        try {
            String raw = errorBody.string();
            if (raw == null || raw.trim().isEmpty()) {
                return new ParsedApiError(message, fieldErrors);
            }

            JsonElement rootElement = JsonParser.parseString(raw);
            if (!rootElement.isJsonObject()) {
                return new ParsedApiError(message, fieldErrors);
            }

            JsonObject root = rootElement.getAsJsonObject();

            if (root.has("message") && !root.get("message").isJsonNull()) {
                String parsedMessage = root.get("message").getAsString();
                if (parsedMessage != null && !parsedMessage.trim().isEmpty()) {
                    message = parsedMessage;
                }
            }

            if (root.has("errors") && root.get("errors").isJsonObject()) {
                JsonObject errors = root.getAsJsonObject("errors");
                for (Map.Entry<String, JsonElement> entry : errors.entrySet()) {
                    String key = entry.getKey();
                    JsonElement value = entry.getValue();

                    if (value == null || value.isJsonNull()) {
                        continue;
                    }

                    if (value.isJsonArray()) {
                        JsonArray array = value.getAsJsonArray();
                        if (!array.isEmpty() && !array.get(0).isJsonNull()) {
                            fieldErrors.put(key, array.get(0).getAsString());
                        }
                    } else {
                        fieldErrors.put(key, value.getAsString());
                    }
                }
            }
        } catch (IOException ignored) {
            return new ParsedApiError(message, fieldErrors);
        } catch (RuntimeException ignored) {
            return new ParsedApiError(message, fieldErrors);
        }

        return new ParsedApiError(message, fieldErrors);
    }

    public static final class ParsedApiError {
        private final String message;
        private final Map<String, String> fieldErrors;

        public ParsedApiError(String message, Map<String, String> fieldErrors) {
            this.message = message;
            this.fieldErrors = fieldErrors == null ? Collections.emptyMap() : fieldErrors;
        }

        public String getMessage() {
            return message;
        }

        public Map<String, String> getFieldErrors() {
            return fieldErrors;
        }
    }
}
