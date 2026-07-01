package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class TenantDashboardResponse {

    @SerializedName("current_unit")
    private CurrentUnitDto currentUnit;

    @SerializedName("rent_status")
    private String rentStatus;

    @SerializedName("payment_summary")
    private DashboardStats paymentSummary;

    @SerializedName("recent_payments")
    private List<DashboardPaymentDto> recentPayments;

    @SerializedName("maintenance_requests")
    private List<DashboardRequestDto> maintenanceRequests;

    @SerializedName("notifications")
    private List<NotificationItem> notifications;

    public CurrentUnitDto getCurrentUnit() { return currentUnit; }
    public String getRentStatus() { return rentStatus; }
    public DashboardStats getPaymentSummary() { return paymentSummary; }
    public List<DashboardPaymentDto> getRecentPayments() { return recentPayments; }
    public List<DashboardRequestDto> getMaintenanceRequests() { return maintenanceRequests; }
    public List<NotificationItem> getNotifications() { return notifications; }
}
