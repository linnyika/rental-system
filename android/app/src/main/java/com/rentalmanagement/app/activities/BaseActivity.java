package com.rentalmanagement.app.activities;

import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import com.rentalmanagement.app.utilities.AlertDialogUtil;
import com.rentalmanagement.app.utilities.LoadingDialog;
import com.rentalmanagement.app.utilities.NetworkUtils;

public abstract class BaseActivity extends AppCompatActivity {

    private LoadingDialog loadingDialog;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    protected void showLoading() {
        if (loadingDialog == null) {
            loadingDialog = new LoadingDialog(this);
        }
        loadingDialog.show();
    }

    protected void hideLoading() {
        if (loadingDialog != null) {
            loadingDialog.dismiss();
        }
    }

    protected void showInfoAlert(String title, String message) {
        AlertDialogUtil.showInfo(this, title, message);
    }

    protected void showErrorAlert(String title, String message) {
        AlertDialogUtil.showError(this, title, message);
    }

    protected boolean isNetworkAvailable() {
        return NetworkUtils.isNetworkAvailable(this);
    }

    protected void configureToolbar(Toolbar toolbar, String title, boolean showUpButton) {
        setSupportActionBar(toolbar);

        ActionBar actionBar = getSupportActionBar();
        if (actionBar != null) {
            actionBar.setTitle(title);
            actionBar.setDisplayHomeAsUpEnabled(showUpButton);
        }

        if (showUpButton) {
            toolbar.setNavigationOnClickListener(v -> getOnBackPressedDispatcher().onBackPressed());
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        getOnBackPressedDispatcher().onBackPressed();
        return true;
    }
}
