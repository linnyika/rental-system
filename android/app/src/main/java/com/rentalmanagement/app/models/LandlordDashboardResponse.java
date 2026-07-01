package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class LandlordDashboardResponse {

    @SerializedName("stats")
    private DashboardStats stats;

    @SerializedName("recent_payments")
    private List<DashboardPaymentDto> recentPayments;

    @SerializedName("maintenance_requests")
    private List<DashboardRequestDto> maintenanceRequests;

    @SerializedName("notifications")
    private List<NotificationItem> notifications;

    public DashboardStats getStats() { return stats; }
    public List<DashboardPaymentDto> getRecentPayments() { return recentPayments; }
    public List<DashboardRequestDto> getMaintenanceRequests() { return maintenanceRequests; }
    public List<NotificationItem> getNotifications() { return notifications; }
}
