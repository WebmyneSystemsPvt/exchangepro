import { round } from 'lodash';
import React from 'react';
import {
  Button,
  Card,
  CardBody,
  CardFooter,
  CardText,
  CardTitle
} from 'reactstrap';


const CardComp = ({ item, RedirectPage, }) => {
  return (
    <Card>
      <div className='Card_Img'>
        <img src={item?.default_storage_photo} top width="100%" alt="Card image cap" />
        <span>{item.category?.name}</span>
        <small><i class="fas fa-star"></i>{item.status}</small>
      </div>
      <CardBody>
        <CardTitle tag="h5">{item.listing_type}</CardTitle>
        <CardText>{item.description.slice(0, 100) + "..."}</CardText>
        <span> <i class="fal fa-map-marker-alt"></i> {round(item?.distance?.miles)} miles away</span>
      </CardBody>
      <CardFooter>
        <strong>{item.currency}{item.rate}</strong>
        <Button onClick={() => RedirectPage(item)}>View Details</Button>
      </CardFooter>
    </Card>
  );
};

export default CardComp;