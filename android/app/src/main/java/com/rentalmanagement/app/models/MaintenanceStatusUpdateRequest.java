package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class MaintenanceStatusUpdateRequest {

    @SerializedName("status")
    private final String status;

    public MaintenanceStatusUpdateRequest(String status) {
        this.status = status;
    }
}
