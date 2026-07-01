package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class AdminDashboardResponse {

    @SerializedName("stats")
    private DashboardStats stats;

    @SerializedName("recent_registrations")
    private List<DashboardUserDto> recentRegistrations;

    @SerializedName("recent_payments")
    private List<DashboardPaymentDto> recentPayments;

    @SerializedName("recent_activity")
    private List<DashboardActivityDto> recentActivity;

    public DashboardStats getStats() { return stats; }
    public List<DashboardUserDto> getRecentRegistrations() { return recentRegistrations; }
    public List<DashboardPaymentDto> getRecentPayments() { return recentPayments; }
    public List<DashboardActivityDto> getRecentActivity() { return recentActivity; }
}
