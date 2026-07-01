package com.rentalmanagement.app.fragments;

import android.content.Context;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import com.rentalmanagement.app.utilities.AlertDialogUtil;
import com.rentalmanagement.app.utilities.LoadingDialog;
import com.rentalmanagement.app.utilities.NetworkUtils;

public abstract class BaseFragment extends Fragment {

    private LoadingDialog loadingDialog;

    @Override
    public void onAttach(@NonNull Context context) {
        super.onAttach(context);
    }

    protected void showLoading() {
        Context context = getContext();
        if (context == null) {
            return;
        }
        if (loadingDialog == null) {
            loadingDialog = new LoadingDialog(context);
        }
        loadingDialog.show();
    }

    protected void hideLoading() {
        if (loadingDialog != null) {
            loadingDialog.dismiss();
        }
    }

    protected void showInfoAlert(String title, String message) {
        Context context = getContext();
        if (context != null) {
            AlertDialogUtil.showInfo(context, title, message);
        }
    }

    protected void showErrorAlert(String title, String message) {
        Context context = getContext();
        if (context != null) {
            AlertDialogUtil.showError(context, title, message);
        }
    }

    protected boolean isNetworkAvailable() {
        Context context = getContext();
        return context != null && NetworkUtils.isNetworkAvailable(context);
    }
}
