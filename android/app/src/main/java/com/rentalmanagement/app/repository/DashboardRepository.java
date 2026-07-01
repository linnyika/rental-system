package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.api.ApiClient;
import com.rentalmanagement.app.api.DashboardApiService;
import com.rentalmanagement.app.models.AdminDashboardResponse;
import com.rentalmanagement.app.models.CaretakerDashboardResponse;
import com.rentalmanagement.app.models.LandlordDashboardResponse;
import com.rentalmanagement.app.models.TenantDashboardResponse;

import retrofit2.Call;

public class DashboardRepository {

    private final DashboardApiService dashboardApiService;

    public DashboardRepository() {
        dashboardApiService = ApiClient.getRetrofit().create(DashboardApiService.class);
    }

    public Call<AdminDashboardResponse> adminDashboard(String authorization) {
        return dashboardApiService.adminDashboard(authorization);
    }

    public Call<LandlordDashboardResponse> landlordDashboard(String authorization) {
        return dashboardApiService.landlordDashboard(authorization);
    }

    public Call<CaretakerDashboardResponse> caretakerDashboard(String authorization) {
        return dashboardApiService.caretakerDashboard(authorization);
    }

    public Call<TenantDashboardResponse> tenantDashboard(String authorization) {
        return dashboardApiService.tenantDashboard(authorization);
    }
}
