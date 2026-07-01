package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ActivityLogsResponse {

    @SerializedName("logs")
    private List<ActivityLogItem> logs;

    public List<ActivityLogItem> getLogs() {
        return logs;
    }
}
