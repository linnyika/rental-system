package com.rentalmanagement.app.utilities;

import android.content.Context;
import android.content.Intent;

import com.rentalmanagement.app.activities.AdminDashboardActivity;
import com.rentalmanagement.app.activities.CaretakerDashboardActivity;
import com.rentalmanagement.app.activities.LandlordDashboardActivity;
import com.rentalmanagement.app.activities.LoginActivity;
import com.rentalmanagement.app.activities.TenantDashboardActivity;

public final class NavigationUtils {

    private NavigationUtils() {
    }

    public static Intent loginIntent(Context context) {
        return new Intent(context, LoginActivity.class);
    }

    public static Intent dashboardIntent(Context context, String role) {
        if ("admin".equalsIgnoreCase(role)) {
            return new Intent(context, AdminDashboardActivity.class);
        }
        if ("landlord".equalsIgnoreCase(role)) {
            return new Intent(context, LandlordDashboardActivity.class);
        }
        if ("caretaker".equalsIgnoreCase(role)) {
            return new Intent(context, CaretakerDashboardActivity.class);
        }
        return new Intent(context, TenantDashboardActivity.class);
    }
}
