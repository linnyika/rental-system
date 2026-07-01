package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class TaskActionResponse {

    @SerializedName("message")
    private String message;

    @SerializedName("task")
    private TaskItem task;

    public String getMessage() {
        return message;
    }

    public TaskItem getTask() {
        return task;
    }
}
