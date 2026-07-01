package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class CaretakerDashboardResponse {

    @SerializedName("stats")
    private DashboardStats stats;

    @SerializedName("tasks")
    private List<DashboardTaskDto> tasks;

    @SerializedName("activity_logs")
    private List<DashboardActivityDto> activityLogs;

    @SerializedName("notifications")
    private List<NotificationItem> notifications;

    public DashboardStats getStats() { return stats; }
    public List<DashboardTaskDto> getTasks() { return tasks; }
    public List<DashboardActivityDto> getActivityLogs() { return activityLogs; }
    public List<NotificationItem> getNotifications() { return notifications; }
}
