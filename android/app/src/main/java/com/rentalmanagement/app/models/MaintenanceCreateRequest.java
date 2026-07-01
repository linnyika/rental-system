package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class MaintenanceCreateRequest {

    @SerializedName("description")
    private final String description;

    @SerializedName("is_major")
    private final Boolean isMajor;

    public MaintenanceCreateRequest(String description, Boolean isMajor) {
        this.description = description;
        this.isMajor = isMajor;
    }
}
