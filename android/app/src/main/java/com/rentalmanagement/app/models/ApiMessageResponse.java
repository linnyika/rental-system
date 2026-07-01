package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class ApiMessageResponse {

    @SerializedName("message")
    private String message;

    public String getMessage() {
        return message;
    }
}
