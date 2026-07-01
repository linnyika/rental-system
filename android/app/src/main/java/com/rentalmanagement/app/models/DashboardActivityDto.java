package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class DashboardActivityDto {

    @SerializedName("id")
    private long id;

    @SerializedName("description")
    private String description;

    @SerializedName("activity_date")
    private String activityDate;

    public long getId() { return id; }
    public String getDescription() { return description; }
    public String getActivityDate() { return activityDate; }
}
