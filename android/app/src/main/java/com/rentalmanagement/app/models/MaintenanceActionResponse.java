package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class MaintenanceActionResponse {

    @SerializedName("message")
    private String message;

    @SerializedName("request")
    private MaintenanceRequestItem request;

    public String getMessage() {
        return message;
    }

    public MaintenanceRequestItem getRequest() {
        return request;
    }
}
