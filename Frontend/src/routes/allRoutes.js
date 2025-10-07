import { lazy, Suspense } from "react";
import { PageLoader } from "../common/Loader";


const Dashboard = lazy(() =>
  import("../_components/Dashboard/Dashboard")
);

const MyAccount = lazy(() =>
  import("../_components/MyAccount")
);

const AddItem = lazy(() =>
  import("../_components/Item/AddItem")
);

const ItemList = lazy(() =>
  import("../_components/Item/ItemList")
);

const ItemDetail = lazy(() =>
  import("../_components/Item/ItemDetail")
);

const Login = lazy(() =>
  import("../_components/Auth/Login")
);

const ImageUploadWithTimestamp = lazy(() =>
  import("../_components/Dashboard/ImageUploadWithTimestamp")
);

const Multi = lazy(() =>
  import("../_components/Item/Multi")
);



export const publicRoutes = [
  {
    title: "Dashboard",
    path: "/login",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <Login />
      </Suspense>
    ),
    hideFooter: true,
    hideProfile: true,
  },
  {
    title: "Dashboard",
    path: "/home",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <Dashboard />
      </Suspense>
    ),
  },
  {
    title: "Item List",
    path: "/list-item",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <ItemList />
      </Suspense>
    ),
    header: false,
  },
  {
    title: "Item List",
    path: "/item-details/:id",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <ItemDetail />
      </Suspense>
    ),
  },
  {
    title: "ImageUploadWithTimestamp",
    path: "/image",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <ImageUploadWithTimestamp />
      </Suspense>
    ),
    header: true,
  },
  {
    title: "MultiSTP",
    path: "/multi",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <Multi />
      </Suspense>
    ),
    header: true,
  },
];

export const protectedRoutes = [
  {
    title: "Add Item",
    path: "/add-item",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <AddItem />
      </Suspense>
    ),
    header: true,
  },
  {
    title: "My Account",
    path: "/account",
    Component: () => (
      <Suspense fallback={<PageLoader />}>
        <MyAccount />
      </Suspense>
    ),
    header: true,
  },
];
