import { createBrowserHistory } from "history";
import { useState } from "react";

export const history = createBrowserHistory();

export function validateEmail(email) {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

export const currentYear = new Date().getFullYear();

export const ChangeKeyValue = (arr = [], keyToChange) => {
  if (arr.length > 0 && keyToChange) {
    return arr.map((item) => {
      return ({
        ...item,
        label: item[keyToChange],
        value: item.id  // Assuming 'id' is always the value you want to set
      });
    });
  }
  return [];
};

export const convertToFormData = (obj) => {
  const formData = new FormData();
  for (const key in obj) {
    if (Array.isArray(obj[key])) {
      obj[key].forEach((item, index) => {
        formData.append(`${key}[]`, item);
      });
    } else {
      formData.append(key, obj[key]);
    }
  }
  return formData;
};


export const useMultiStepForm = (steps) => {
  const [currentStep, setCurrentStep] = useState(0);
  const next = () => {
    setCurrentStep((prev) => {
      if (prev < steps.length - 1) return prev + 1;
      return prev;
    });
  };

  const back = () => {
    setCurrentStep((prev) => {
      if (prev > 0) return prev - 1;
      return prev;
    });
  };

  const goTo = (index) => {
    setCurrentStep(index);
  };

  return {
    next,
    back,
    goTo,
    Step: steps[currentStep],
    currentStep,
    isLastStep: currentStep === steps.length - 1,
    isFirstStep: currentStep === 0,
  };
};




export const MultiStepsControllers = ({
  back,
  isFirstStep,
  isLastStep,
  submitLoader = false,
  hideButton = false,
}) => {
  return (
    <div className="form-controllers">
      <button data-tooltip-content="Previous" id="Previous" onClick={back} disabled={isFirstStep} className="right_bt">
        <i class="fas fa-chevron-left"></i>
      </button>
      {isLastStep ? !submitLoader ?
        (!hideButton && <button class="btn btn-primary px-4 ms-3" form="customer" type="submit">
          Submit
        </button>) :
        <button class="btn btn-primary px-4 ms-3" form="customer" type="submit">
          loading...
        </button> : (
        <button data-tooltip-content="Next" id="Next" form="customer" type="submit" className="right_bt">
          <i class="fas fa-chevron-right"></i>
        </button>
      )}
    </div>
  );
};