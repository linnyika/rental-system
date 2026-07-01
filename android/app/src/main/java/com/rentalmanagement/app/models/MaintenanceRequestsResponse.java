package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class MaintenanceRequestsResponse {

    @SerializedName("requests")
    private List<MaintenanceRequestItem> requests;

    public List<MaintenanceRequestItem> getRequests() {
        return requests;
    }
}
