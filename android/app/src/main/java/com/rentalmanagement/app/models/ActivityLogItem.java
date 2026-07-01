package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class ActivityLogItem {

    @SerializedName("id")
    private long id;

    @SerializedName("caretaker_id")
    private Long caretakerId;

    @SerializedName("description")
    private String description;

    @SerializedName("activity_date")
    private String activityDate;

    @SerializedName("created_at")
    private String createdAt;

    public long getId() {
        return id;
    }

    public Long getCaretakerId() {
        return caretakerId;
    }

    public String getDescription() {
        return description;
    }

    public String getActivityDate() {
        return activityDate;
    }

    public String getCreatedAt() {
        return createdAt;
    }
}
